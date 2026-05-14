<?php

use App\Http\Controllers\AdminUser\AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exercise\ExerciseController;
use App\Http\Controllers\ExerciseLibrary\ExerciseLibraryController;
use App\Http\Controllers\Friends\FriendController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\Stats\StatsController;
use App\Http\Controllers\TrainingPlan\TrainingPlanController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Workouts\WorkoutController;
use App\Models\CoachNote;
use App\Models\ExerciseLibrary;
use App\Models\Message;
use App\Models\TrainerClient;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/home', [StatsController::class, 'home']);
Route::get('/stats', [StatsController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/messages/{userId}', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'store']);

    Route::get('/workouts', [TrainingPlanController::class, 'index']);
    Route::get('/workouts/{id}', [TrainingPlanController::class, 'show']);
    Route::post('/workouts', [WorkoutController::class, 'store']);

    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);
    Route::post('/exercises', [ExerciseController::class, 'store']);

    Route::get('/exercise-library', function () {
        return ExerciseLibrary::all();
    });
    Route::post('/exercise-library', [ExerciseLibraryController::class, 'store']);

    Route::get('/users', [FriendController::class, 'search']);
    Route::get('/users/{id}', [UserController::class, 'show']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/friends', [FriendController::class, 'index']);
    Route::post('/friends/add', [FriendController::class, 'add']);
    Route::delete('/friends/{id}', [FriendController::class, 'remove']);

    Route::get('/friends/requests', [FriendController::class, 'requests']);
    Route::post('/friends/accept/{id}', [FriendController::class, 'accept']);
    Route::delete('/friends/decline/{id}', [FriendController::class, 'decline']);

    Route::get('/me', [FriendController::class, 'me']);
    Route::put('/me', [FriendController::class, 'update']);
    Route::post('/me/photo', [FriendController::class, 'uploadPhoto']);
    Route::put('/me/password', [FriendController::class, 'updatePassword']);

    Route::get('/admin/users', function () {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return User::all();
    });

    Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);

    Route::get('/trainer/users', function () {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return User::where('role', 'user')->get();
    });

    Route::get('/trainer/clients', function () {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return auth()->user()->clients()->wherePivot('status', 'accepted')->get();
    });

    Route::post('/trainer/add-client/{id}', function ($id) {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        TrainerClient::firstOrCreate(
            ['trainer_id' => auth()->id(), 'client_id' => $id],
            ['status' => 'pending']
        );

        return response()->json(['message' => 'Request sent']);
    });

    Route::get('/trainer/client/{id}/notes', function ($id) {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return CoachNote::where('trainer_id', auth()->id())
            ->where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
    });

    Route::post('/trainer/client/{id}/notes', function (Request $request, $id) {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $note = CoachNote::create([
            'trainer_id' => auth()->id(),
            'client_id' => $id,
            'note' => $request->input('note'),
        ]);

        return response()->json($note, 201);
    });

    Route::delete('/trainer/client/notes/{id}', function ($id) {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $note = CoachNote::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();

        $note->delete();

        return response()->json(['message' => 'Note deleted']);
    });

    Route::put('/trainer/client/{id}', function (Request $request, $id) {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $client = User::findOrFail($id);
        $client->update($request->only(['goal', 'weight']));

        return response()->json($client);
    });

    Route::put('/trainer/client/{id}', function (Request $request, $id) {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $client = User::findOrFail($id);
        $client->update($request->only(['goal', 'weight', 'height', 'age', 'gender', 'bio']));

        return response()->json($client);
    });

    Route::get('/my/trainer-requests', function () {
        $pending = TrainerClient::where('client_id', auth()->id())
            ->where('status', 'pending')
            ->with('trainer')
            ->get();

        return $pending->map(fn ($r) => [
            'id' => $r->trainer->id,
            'name' => $r->trainer->name,
            'profile_photo' => $r->trainer->profile_photo,
        ]);
    });

    Route::post('/my/trainer-requests/accept/{trainerId}', function ($trainerId) {
        TrainerClient::where('trainer_id', $trainerId)
            ->where('client_id', auth()->id())
            ->update(['status' => 'accepted']);

        return response()->json(['message' => 'Accepted']);
    });

    Route::delete('/my/trainer-requests/decline/{trainerId}', function ($trainerId) {
        TrainerClient::where('trainer_id', $trainerId)
            ->where('client_id', auth()->id())
            ->delete();

        return response()->json(['message' => 'Declined']);
    });

    Route::post('/workout-logs/start', function (Request $request) {
        $log = WorkoutLog::create([
            'user_id' => auth()->id(),
            'training_plan_id' => $request->training_plan_id,
        ]);

        return response()->json($log);
    });

    Route::post('/workout-logs/{id}/finish', function (Request $request, $id) {
        $log = WorkoutLog::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $log->update([
            'duration_seconds' => $request->duration_seconds,
        ]);

        foreach ($request->sets as $set) {
            WorkoutLogSet::create([
                'workout_log_id' => $log->id,
                'exercise_name' => $set['exercise_name'],
                'set_number' => $set['set_number'],
                'reps' => $set['reps'],
                'weight' => $set['weight'],
            ]);
        }

        auth()->user()->increment('completed_workouts');

        return response()->json(['message' => 'Workout saved']);
    });

    Route::get('/workout-logs', function () {
        return WorkoutLog::where('user_id', auth()->id())
            ->with('sets')
            ->orderBy('created_at', 'desc')
            ->get();
    });

    Route::get('/trainer/clients-all', function () {
        if (auth()->user()->role !== 'trainer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $relations = TrainerClient::where('trainer_id', auth()->id())
            ->with('client')
            ->get();

        return $relations->map(fn ($r) => [
            'id' => $r->client->id,
            'name' => $r->client->name,
            'email' => $r->client->email,
            'profile_photo' => $r->client->profile_photo,
            'status' => $r->status,
        ]);
    });

    Route::get('/my/trainer', function () {
        $relation = TrainerClient::where('client_id', auth()->id())
            ->where('status', 'accepted')
            ->with('trainer')
            ->first();

        if (! $relation) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $relation->trainer->id,
            'name' => $relation->trainer->name,
            'profile_photo' => $relation->trainer->profile_photo,
        ]);
    });

    Route::get('/messages/{userId}/last', function ($userId) {
        $last = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', auth()->id());
        })
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $last) {
            return response()->json(null);
        }

        return response()->json([
            'message' => $last->message,
            'sender_id' => $last->sender_id,
            'read_at' => $last->read_at,
            'created_at' => $last->created_at,
        ]);
    });
});
