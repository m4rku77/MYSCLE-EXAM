<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingPlan extends Model
{
    use HasFactory;

    public const TABLE = 'training_plans';

    public const ID = 'id';

    public const USER_ID = 'user_id';

    public const NAME = 'name';

    public const IS_FAVORITE = 'is_favorite';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const NOTES = 'notes';
    
    protected $table = self::TABLE;

    protected $fillable = [
        self::USER_ID,
        self::NAME,
        self::IS_FAVORITE,
        self::NOTES,
    ];
    protected $casts = [
        self::IS_FAVORITE => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::USER_ID,
            User::ID
        );
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }
    
}
