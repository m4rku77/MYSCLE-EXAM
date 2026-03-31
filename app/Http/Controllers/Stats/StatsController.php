<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'workouts' => TrainingPlan::count(),
            'users' => User::count(),
            // 'trainers' =>
        ]);
    }
}
