<?php

use App\Http\Controllers\AdminUser\AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Exercise\ExerciseController;
use App\Http\Controllers\ExerciseLibrary\ExerciseLibraryController;
use App\Http\Controllers\Friends\FriendController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\Stats\StatsController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\TrainerClient\TrainerClientController;
use App\Http\Controllers\TrainingPlan\TrainingPlanController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Workouts\WorkoutController;
use App\Http\Controllers\WorkoutLog\WorkoutLogController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/home', [StatsController::class, 'home']);
Route::get('/stats', [StatsController::class, 'index']);
Route::post('/stripe/webhook', [SubscriptionController::class, 'webhook']);
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/user', [FriendController::class, 'me']);

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{id}', [AdminUserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::get('/trainer-clients', [AdminUserController::class, 'trainerClients']);
        Route::get('/workout-logs', [AdminUserController::class, 'workoutLogs']);
        Route::get('/subscriptions', [AdminUserController::class, 'subscriptions']);
        Route::get('/training-plans', [AdminUserController::class, 'trainingPlans']);
        Route::get('/friends', [AdminUserController::class, 'friends']);
    });

    // Messages
    Route::get('/messages/{userId}/last', [MessageController::class, 'lastMessage']);
    Route::get('/messages/{userId}', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'store']);

    // Workouts
    Route::get('/workouts', [TrainingPlanController::class, 'index']);
    Route::get('/workouts/{id}', [TrainingPlanController::class, 'show']);
    Route::post('/workouts', [WorkoutController::class, 'store']);
    Route::put('/workouts/{id}', [WorkoutController::class, 'update']);
    Route::delete('/workouts/{id}', [WorkoutController::class, 'destroy']);


    // Exercises
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);

    // Exercise Library
    Route::get('/exercise-library', [ExerciseLibraryController::class, 'index']);
    Route::post('/exercise-library', [ExerciseLibraryController::class, 'store']);

    // Users
    Route::get('/users', [FriendController::class, 'search']);
    Route::get('/users/{id}', [UserController::class, 'show']);

    // Friends
    Route::get('/friends/requests', [FriendController::class, 'requests']);
    Route::post('/friends/accept/{id}', [FriendController::class, 'accept']);
    Route::delete('/friends/decline/{id}', [FriendController::class, 'decline']);
    Route::get('/friends', [FriendController::class, 'index']);
    Route::post('/friends/add', [FriendController::class, 'add']);
    Route::delete('/friends/{id}', [FriendController::class, 'remove']);

    // Profile
    Route::get('/me', [FriendController::class, 'me']);
    Route::put('/me', [FriendController::class, 'update']);
    Route::post('/me/photo', [FriendController::class, 'uploadPhoto']);
    Route::put('/me/password', [FriendController::class, 'updatePassword']);

    // Trainer
    Route::prefix('trainer')->group(function () {
        Route::get('/users', [TrainerClientController::class, 'users']);
        Route::get('/clients', [TrainerClientController::class, 'clients']);
        Route::get('/clients-all', [TrainerClientController::class, 'allClients']);
        Route::post('/add-client/{id}', [TrainerClientController::class, 'addClient']);

        Route::prefix('client/{id}')->group(function () {
            Route::put('/', [TrainerClientController::class, 'updateClient']);
            Route::delete('/', [TrainerClientController::class, 'removeClient']);
            Route::get('/notes', [TrainerClientController::class, 'getNotes']);
            Route::post('/notes', [TrainerClientController::class, 'addNote']);
            Route::get('/workouts', [TrainerClientController::class, 'getWorkouts']);
            Route::post('/workouts', [TrainerClientController::class, 'createWorkout']);
            Route::get('/stats', [TrainerClientController::class, 'getStats']);
            Route::post('/workout-logs/start', [TrainerClientController::class, 'startWorkout']);
        });

        Route::post('/client/{clientId}/workout-logs/{id}/finish', [TrainerClientController::class, 'finishWorkout']);
        Route::delete('/client/notes/{id}', [TrainerClientController::class, 'deleteNote']);
    });

    // My trainer requests
    Route::get('/my/trainer-requests', [TrainerClientController::class, 'myTrainerRequests']);
    Route::post('/my/trainer-requests/accept/{trainerId}', [TrainerClientController::class, 'acceptTrainerRequest']);
    Route::delete('/my/trainer-requests/decline/{trainerId}', [TrainerClientController::class, 'declineTrainerRequest']);
    Route::get('/my/trainer', [TrainerClientController::class, 'myTrainer']);

    // Workout logs (user)
    Route::get('/workout-logs', [WorkoutLogController::class, 'index']);
    Route::post('/workout-logs/start', [WorkoutLogController::class, 'start']);
    Route::post('/workout-logs/{id}/finish', [WorkoutLogController::class, 'finish']);

    // Subscription
    Route::get('/my/subscription', [SubscriptionController::class, 'show']);
    Route::delete('/my/subscription', [SubscriptionController::class, 'cancel']);
    Route::post('/stripe/checkout', [SubscriptionController::class, 'checkout']);
});