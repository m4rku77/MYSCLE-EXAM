<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserLogicRepository
{
    public function __construct(
        private readonly UserDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function getById(int $id): User
    {
        $user = $this->db->findById($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        return $user;
    }

    public function getWithStats(int $id): array
    {
        $user = $this->db->findByIdWithStats($id);

        $sets = 0;
        $reps = 0;

        foreach ($user->trainingPlans as $plan) {
            foreach ($plan->exercises as $ex) {
                foreach ($ex->exerciseSets as $set) {
                    $sets++;
                    $reps += $set->reps;
                }
            }
        }

        return [
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'profile_photo'      => $user->profile_photo
                ? (str_starts_with($user->profile_photo, 'http')
                    ? $user->profile_photo
                    : asset('storage/' . $user->profile_photo))
                : null,
            'created_at'         => $user->created_at,
            'location'           => $user->location ?? 'Unknown',
            'bio'                => $user->bio,
            'goal'               => $user->goal,
            'weight'             => $user->weight,
            'height'             => $user->height,
            'age'                => $user->age,
            'gender'             => $user->gender,
            'completed_workouts' => $user->completed_workouts,
            'stats'              => [
                'workouts' => $user->trainingPlans->count(),
                'sets'     => $sets,
                'reps'     => $reps,
            ],
        ];
    }

    public function register(array $data): User
    {
        return $this->db->create([
            'name'     => $data['first_name'] . ' ' . $data['last_name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'user',
        ]);
    }

    public function login(array $data): array
    {
        $user = $this->db->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function update(User $user, array $data): User
    {
        return $this->db->update($user, $data);
    }

    public function delete(int $id): void
    {
        $user = $this->db->findById($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        $this->db->delete($user);
    }
}