<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exercise\ExerciseController;
use App\Http\Controllers\Stats\StatsController;
use App\Http\Controllers\TrainingPlan\TrainingPlanController;
use App\Http\Controllers\Friends\FriendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/stats', [StatsController::class, 'index']);

Route::middleware('auth:sanctum')->get('/workouts', [TrainingPlanController::class, 'index']);
Route::middleware('auth:sanctum')->get('/workouts/{id}', [TrainingPlanController::class, 'show']);
Route::middleware('auth:sanctum')->put('/exercises/{id}', [ExerciseController::class, 'update']);
Route::get('/users', [FriendController::class, 'search']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/friends', [FriendController::class, 'index']);
    Route::post('/friends/add', [FriendController::class, 'add']);
    Route::delete('/friends/{id}', [FriendController::class, 'remove']);

});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
