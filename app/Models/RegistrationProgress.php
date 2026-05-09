<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationProgress extends Model
{
    protected $table = 'registration_progress';

    protected $fillable = [
        'user_id',
        'current_step',
        'administration_status',
        'administration_data',
        'administration_admin_note',
        'health_status',
        'health_data',
        'health_admin_note',
        'psychology_status',
        'psychology_data',
        'psychology_admin_note',
        'physical_status',
        'physical_data',
        'physical_admin_note',
        'fully_completed',
    ];

    protected function casts(): array
    {
        return [
            'administration_data' => 'array',
            'health_data' => 'array',
            'psychology_data' => 'array',
            'physical_data' => 'array',
            'fully_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
