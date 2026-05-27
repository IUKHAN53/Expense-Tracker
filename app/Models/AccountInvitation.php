<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AccountInvitation extends Model
{
    protected $fillable = [
        'account_id',
        'invited_by_user_id',
        'email',
        'token',
        'expires_at',
        'accepted_at',
        'accepted_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function isPending(): bool
    {
        return ! $this->accepted_at && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function newToken(): string
    {
        // 32 bytes, hex-encoded = 64 chars. Fits the schema, fits a URL.
        return bin2hex(random_bytes(32));
    }

    public function url(): string
    {
        return url('/invitation?'.http_build_query([
            'token' => $this->token,
            'email' => $this->email,
        ]));
    }
}
