<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'workouts' => \App\Models\WorkoutLog::count(),
            'users' => \App\Models\User::count(),
            'trainers' => \App\Models\User::where('role', 'trainer')->count(),
        ]);
    }
}
