<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Batch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'starts_on',
        'ends_on',
        'is_active',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date:Y-m-d',
            'ends_on' => 'date:Y-m-d',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'batch_user')
            ->withTimestamps();
    }

    public function bimbleClasses(): BelongsToMany
    {
        return $this->belongsToMany(BimbleClass::class, 'batch_bimble_class')
            ->withTimestamps();
    }
}
