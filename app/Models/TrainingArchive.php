<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingArchive extends Model
{
    public const TABLE = 'training_archives';

    public const ID = 'id';

    public const USER_ID = 'user_id';

    public const TRAINING_PLAN_ID = 'training_plan_id';

    public const COMPLETED_AT = 'completed_at';

    public const NOTES = 'notes';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE;

    protected $fillable = [
        self::USER_ID,
        self::TRAINING_PLAN_ID,
        self::COMPLETED_AT,
        self::NOTES,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::USER_ID,
            User::ID
        );
    }

    public function trainingPlan(): BelongsTo
    {
        return $this->belongsTo(
            TrainingPlan::class,
            self::TRAINING_PLAN_ID,
            TrainingPlan::ID
        );
    }
}
