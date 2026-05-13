<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const TABLE = 'users';

    public const ID = 'id';

    public const USERNAME = 'username';

    public const FIRST_NAME = 'first_name';

    public const LAST_NAME = 'last_name';

    public const EMAIL = 'email';

    public const PASSWORD = 'password';

    public const ROLE = 'role';

    public const STATUS = 'status';

    public const PROFILE_IMAGE = 'profile_image';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE;

    protected $fillable = [
        'name',
        self::EMAIL,
        self::PASSWORD,
        self::ROLE,
        self::PROFILE_IMAGE,
    ];

    protected $hidden = [
        self::PASSWORD,
    ];

    protected $casts = [
        self::PASSWORD => 'hashed',
    ];

    public function trainingPlans(): HasMany
    {
        return $this->hasMany(
            TrainingPlan::class,
            TrainingPlan::USER_ID,
            self::ID
        );
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(
            Message::class,
            Message::SENDER_ID,
            self::ID
        );
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(
            Message::class,
            Message::RECEIVER_ID,
            self::ID
        );
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(
            Subscription::class,
            Subscription::USER_ID,
            self::ID
        );
    }

    public function trainerRelations(): HasMany
    {
        return $this->hasMany(
            TrainerClient::class,
            TrainerClient::TRAINER_ID,
            self::ID
        );
    }

    public function athleteRelations(): HasMany
    {
        return $this->hasMany(
            TrainerClient::class,
            TrainerClient::ATHLETE_ID,
            self::ID
        );
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    public function clients()
    {
        return $this->belongsToMany(
            User::class,
            'trainer_clients',
            'trainer_id',
            'client_id'
        );
    }

    public function trainers()
    {
        return $this->belongsToMany(
            User::class,
            'trainer_clients',
            'client_id',
            'trainer_id'
        );
    }
}
