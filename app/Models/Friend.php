<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friend extends Model
{
    public const TABLE = 'friends';
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const FRIEND_ID = 'friend_id';
    public const STATUS = 'status';

    protected $table = self::TABLE;

    protected $fillable = [
        self::USER_ID,
        self::FRIEND_ID,
        self::STATUS,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID);
    }

    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, self::FRIEND_ID);
    }
}