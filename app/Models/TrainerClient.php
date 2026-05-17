<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerClient extends Model
{
    public const TABLE = 'trainer_clients';

    public const ID = 'id';

    public const TRAINER_ID = 'trainer_id';

    public const CLIENT_ID = 'client_id';

    public const STATUS = 'status';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE;

    protected $fillable = [
        self::TRAINER_ID,
        self::CLIENT_ID,
        self::STATUS,
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, self::TRAINER_ID, User::ID);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, self::CLIENT_ID, User::ID);
    }
}
