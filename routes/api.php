<?php

use App\Http\Controllers\TrainingPlanController;

Route::apiResource('training-plans', TrainingPlanController::class);
Route::apiResource('exercises', ExerciseController::class);

?>