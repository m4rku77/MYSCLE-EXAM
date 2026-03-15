<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    public const TABLE = 'subscriptions';

    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const TYPE = 'type';
    public const START_DATE = 'start_date';
    public const END_DATE = 'end_date';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE;

    protected $fillable = [
        self::USER_ID,
        self::TYPE,
        self::START_DATE,
        self::END_DATE,
        self::STATUS,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::USER_ID,
            User::ID
        );
    }
}