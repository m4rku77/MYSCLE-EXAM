<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exercise\ExerciseController;
use App\Http\Controllers\Friends\FriendController;
use App\Http\Controllers\Stats\StatsController;
use App\Http\Controllers\TrainingPlan\TrainingPlanController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Workouts\WorkoutController;
use App\Http\Controllers\AdminUser\AdminUserController;
use App\Http\Controllers\ExerciseLibrary\ExerciseLibraryController;
use App\Models\User;
use App\Models\ExerciseLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/home', [StatsController::class, 'home']);
Route::get('/stats', [StatsController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

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

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/admin/users', function () {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return User::all();
    });

    Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);
});