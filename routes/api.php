<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\TrainerClientController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TrainingArchiveController;

Route::apiResource('training-plans', TrainingPlanController::class);
Route::apiResource('exercises', ExerciseController::class);
Route::apiResource('trainer-clients', TrainerClientController::class);
Route::apiResource('messages', MessageController::class);
Route::apiResource('subscriptions', SubscriptionController::class);
Route::apiResource('training-archives', TrainingArchiveController::class);