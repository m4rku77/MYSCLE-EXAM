<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exercise\ExerciseController;
use App\Http\Controllers\Stats\StatsController;
use App\Http\Controllers\Workouts\WorkoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/stats', [StatsController::class, 'index']);

Route::middleware('auth:sanctum')->get('/workouts', [WorkoutController::class, 'index']);
Route::middleware('auth:sanctum')->get('/workouts/{id}', [WorkoutController::class, 'show']);

Route::middleware('auth:sanctum')->get('/workouts/{id}/track', [WorkoutController::class, 'track']);

Route::middleware('auth:sanctum')->put('/exercises/{id}', [ExerciseController::class, 'update']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
