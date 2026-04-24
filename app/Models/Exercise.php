<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    use HasFactory;

    public const TABLE = 'exercises';

    public const ID = 'id';

    public const TRAINING_PLAN_ID = 'training_plan_id';

    public const NAME = 'name';

    public const SETS = 'sets';

    public const REPS = 'reps';

    public const WEIGHT = 'weight';

    public const NOTES = 'notes';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE;

    protected $fillable = [
        self::TRAINING_PLAN_ID,
        self::NAME,
        self::SETS,
        self::REPS,
        self::WEIGHT,
        self::NOTES,
    ];

    public function trainingPlan(): BelongsTo
    {
        return $this->belongsTo(
            TrainingPlan::class,
            self::TRAINING_PLAN_ID,
            TrainingPlan::ID
        );
    }

    public function exerciseSets()
    {
        return $this->hasMany(ExerciseSet::class, 'exercise_id');
    }
}
