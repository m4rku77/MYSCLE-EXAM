<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exercise\ExerciseController;
use App\Http\Controllers\Friends\FriendController;
use App\Http\Controllers\Stats\StatsController;
use App\Http\Controllers\TrainingPlan\TrainingPlanController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Workouts\WorkoutController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/home', [StatsController::class, 'home']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/stats', [StatsController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    // workouts
    Route::get('/workouts', [TrainingPlanController::class, 'index']);
    Route::get('/workouts/{id}', [TrainingPlanController::class, 'show']);
    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);

    Route::post('/workouts', [WorkoutController::class, 'store']);

    // users search
    Route::get('/users', [FriendController::class, 'search']);

    Route::get('/users/{id}', [UserController::class, 'show']);

    // friends
    Route::get('/friends', [FriendController::class, 'index']);
    Route::post('/friends/add', [FriendController::class, 'add']);
    Route::delete('/friends/{id}', [FriendController::class, 'remove']);

    // profile
    Route::get('/me', [FriendController::class, 'me']);
    Route::put('/me', [FriendController::class, 'update']);
    Route::post('/me/photo', [FriendController::class, 'uploadPhoto']);

    Route::get('/friends/requests', [FriendController::class, 'requests']);
    Route::post('/friends/accept/{id}', [FriendController::class, 'accept']);
    Route::delete('/friends/decline/{id}', [FriendController::class, 'decline']);
    Route::put('/me/password', [FriendController::class, 'updatePassword']);

    // auth user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);
    });

});

// admin
Route::middleware(['auth:sanctum'])->get('/admin/users', function () {
    if (auth()->user()->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return User::all();
});
