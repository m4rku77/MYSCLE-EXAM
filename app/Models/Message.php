<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    public const TABLE = 'messages';
    public const ID = 'id';
    public const SENDER_ID = 'sender_id';
    public const RECEIVER_ID = 'receiver_id';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const READ_AT = 'read_at';
    protected $table = self::TABLE;

    protected $fillable = [
        self::SENDER_ID,
        self::RECEIVER_ID,
        self::MESSAGE,
        'read_at',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, self::SENDER_ID, User::ID);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, self::RECEIVER_ID, User::ID);
    }
}
