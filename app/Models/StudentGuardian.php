<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StudentGuardian extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'student_user_id',
        'guardian_user_id',
        'guardian_name',
        'relationship',
        'phone',
        'email',
        'invite_token',
        'invite_status',
        'invited_by',
        'invited_at',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_user_id');
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public static function generateToken(): string
    {
        do {
            $token = Str::lower(Str::random(40));
        } while (static::where('invite_token', $token)->exists());

        return $token;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function relationshipLabel(): string
    {
        return match ($this->relationship) {
            'ayah' => 'Ayah',
            'ibu' => 'Ibu',
            default => 'Wali',
        };
    }
}
