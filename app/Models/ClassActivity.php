<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassActivity extends Model
{
    protected $fillable = [
        'bimble_class_id',
        'created_by',
        'title',
        'description',
        'happened_at',
    ];

    protected function casts(): array
    {
        return [
            'happened_at' => 'datetime',
        ];
    }

    public function bimbleClass(): BelongsTo
    {
        return $this->belongsTo(BimbleClass::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

