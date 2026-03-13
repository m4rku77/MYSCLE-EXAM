<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\TrainerClientController;
use App\Http\Controllers\MessageController;

Route::apiResource('training-plans', TrainingPlanController::class);
Route::apiResource('exercises', ExerciseController::class);
Route::apiResource('trainer-clients', TrainerClientController::class);
Route::apiResource('messages', MessageController::class);

// subscriptions
// training archives