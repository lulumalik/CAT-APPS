<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateIssue extends Model
{
    protected $fillable = [
        'user_id',
        'issued_by',
        'program_category',
        'certificate_number',
        'template_snapshot',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'template_snapshot' => 'array',
            'issued_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
