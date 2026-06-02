<?php

declare(strict_types=1);

namespace App\Http\Controllers\TrainerClient;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainerClient\CreateTrainerClientRequest;
use App\Http\Requests\TrainerClient\UpdateTrainerClientRequest;
use App\Http\Resources\TrainerClientResource;
use App\Models\CoachNote;
use App\Models\TrainerClient;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogSet;
use App\Repositories\TrainerClient\TrainerClientLogicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainerClientController
{
    public function __construct(
        private readonly TrainerClientLogicRepository $logic
    ) {}

    public function index()
    {
        return TrainerClientResource::collection($this->logic->getAll());
    }

    public function show(int $id): TrainerClientResource
    {
        return new TrainerClientResource($this->logic->getById($id));
    }

    public function store(CreateTrainerClientRequest $request): TrainerClientResource
    {
        return new TrainerClientResource($this->logic->create($request->validated()));
    }

    public function update(UpdateTrainerClientRequest $request, int $id): TrainerClientResource
    {
        return new TrainerClientResource($this->logic->update($id, $request->validated()));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);
        return response()->json([], 204);
    }

    // GET /trainer/users
    public function users(): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = \App\Models\User::where('role', 'user')->get();

        return response()->json($users);
    }

    // GET /trainer/clients
    public function clients(): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $clients = auth()->user()->clients()
            ->wherePivot(TrainerClient::STATUS, 'accepted')
            ->get();

        return response()->json($clients);
    }

    // GET /trainer/clients-all
    public function allClients(): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $relations = TrainerClient::where(TrainerClient::TRAINER_ID, auth()->id())
            ->with('client')
            ->get();

        return response()->json($relations->map(fn ($r) => [
            'id' => $r->client->id,
            'name' => $r->client->name,
            'email' => $r->client->email,
            'profile_photo' => $r->client->profile_photo,
            'status' => $r->status,
        ]));
    }

    // POST /trainer/add-client/{id}
    public function addClient(Request $request, int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $client = User::find($id);
        if (!$client) {
            return response()->json(['error' => 'User not found'], 404);
        }

        TrainerClient::firstOrCreate(
            [TrainerClient::TRAINER_ID => auth()->id(), TrainerClient::CLIENT_ID => $id],
            [TrainerClient::STATUS => 'pending']
        );

        return response()->json(['message' => 'Request sent']);
    }

    // DELETE /trainer/client/{id}
    public function removeClient(int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        TrainerClient::where(TrainerClient::TRAINER_ID, auth()->id())
            ->where(TrainerClient::CLIENT_ID, $id)
            ->delete();

        return response()->json(['message' => 'Client removed']);
    }

    // PUT /trainer/client/{id}
    public function updateClient(Request $request, int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'age' => 'nullable|integer|min:0|max:120',
            'gender' => 'nullable|in:male,female,other',
            'goal' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $client = User::findOrFail($id);
        $client->update($request->only(['goal', 'weight', 'height', 'age', 'gender', 'bio']));

        return response()->json($client);
    }

    // GET /trainer/client/{id}/notes
    public function getNotes(int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notes = CoachNote::where('trainer_id', auth()->id())
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notes);
    }

    // POST /trainer/client/{id}/notes
    public function addNote(Request $request, int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $note = CoachNote::create([
            'trainer_id' => auth()->id(),
            'client_id' => $id,
            'note' => $request->input('note'),
        ]);

        return response()->json($note, 201);
    }

    // DELETE /trainer/client/notes/{id}
    public function deleteNote(int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $note = CoachNote::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();

        $note->delete();

        return response()->json(['message' => 'Note deleted']);
    }

    // GET /trainer/client/{id}/workouts
    public function getWorkouts(int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $workouts = TrainingPlan::with('exercises.exerciseSets')
            ->where('user_id', $id)
            ->get();

        return response()->json($workouts);
    }

    // POST /trainer/client/{id}/workouts
    public function createWorkout(Request $request, int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $plan = TrainingPlan::create([
            'user_id' => $id,
            'name' => $request->input('name'),
        ]);

        return response()->json($plan, 201);
    }

    // GET /trainer/client/{id}/stats
    public function getStats(int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = WorkoutLog::where('user_id', $id)
            ->with(['sets', 'trainingPlan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    // POST /trainer/client/{clientId}/workout-logs/start
    public function startWorkout(Request $request, int $clientId): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'training_plan_id' => 'required|integer|exists:training_plans,id',
        ]);

        $log = WorkoutLog::create([
            'user_id' => $clientId,
            'training_plan_id' => $request->training_plan_id,
        ]);

        return response()->json($log);
    }

    // POST /trainer/client/{clientId}/workout-logs/{id}/finish
    public function finishWorkout(Request $request, int $clientId, int $id): JsonResponse
    {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'duration_seconds' => 'required|integer|min:0',
            'sets' => 'required|array',
            'sets.*.exercise_name' => 'required|string',
            'sets.*.set_number' => 'required|integer|min:1',
            'sets.*.reps' => 'required|integer|min:0',
            'sets.*.weight' => 'required|numeric|min:0',
        ]);

        $log = WorkoutLog::where('id', $id)
            ->where('user_id', $clientId)
            ->firstOrFail();

        $log->update(['duration_seconds' => $request->duration_seconds]);

        foreach ($request->input('sets', []) as $set) {
            WorkoutLogSet::create([
                'workout_log_id' => $log->id,
                'exercise_name' => $set['exercise_name'],
                'set_number' => $set['set_number'],
                'reps' => $set['reps'] ?? 0,
                'weight' => $set['weight'] ?? 0,
            ]);
        }

        User::find($clientId)->increment('completed_workouts');

        return response()->json(['message' => 'Workout saved']);
    }

    // GET /my/trainer-requests
    public function myTrainerRequests(): JsonResponse
    {
        $pending = TrainerClient::where(TrainerClient::CLIENT_ID, auth()->id())
            ->where(TrainerClient::STATUS, 'pending')
            ->with('trainer')
            ->get();

        return response()->json($pending->map(fn ($r) => [
            'id' => $r->trainer->id,
            'name' => $r->trainer->name,
            'profile_photo' => $r->trainer->profile_photo,
        ]));
    }

    // POST /my/trainer-requests/accept/{trainerId}
    public function acceptTrainerRequest(int $trainerId): JsonResponse
    {
        TrainerClient::where(TrainerClient::TRAINER_ID, $trainerId)
            ->where(TrainerClient::CLIENT_ID, auth()->id())
            ->update([TrainerClient::STATUS => 'accepted']);

        return response()->json(['message' => 'Accepted']);
    }

    // DELETE /my/trainer-requests/decline/{trainerId}
    public function declineTrainerRequest(int $trainerId): JsonResponse
    {
        TrainerClient::where(TrainerClient::TRAINER_ID, $trainerId)
            ->where(TrainerClient::CLIENT_ID, auth()->id())
            ->delete();

        return response()->json(['message' => 'Declined']);
    }

    // GET /my/trainer
    public function myTrainer(): JsonResponse
    {
        $relation = TrainerClient::where(TrainerClient::CLIENT_ID, auth()->id())
            ->where(TrainerClient::STATUS, 'accepted')
            ->with('trainer')
            ->first();

        if (!$relation) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $relation->trainer->id,
            'name' => $relation->trainer->name,
            'profile_photo' => $relation->trainer->profile_photo,
        ]);
    }
}