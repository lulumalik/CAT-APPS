<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BimbleClass extends Model
{
    protected $fillable = [
        'name',
        'class_code',
        'instructor_name',
        'instructor_id',
        'academic_period',
        'academic_period_start',
        'academic_period_end',
        'participant_count',
        'program_type',
        'cover_image_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'academic_period_start' => 'date:Y-m-d',
            'academic_period_end' => 'date:Y-m-d',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimble_class_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'bimble_class_material')
            ->withPivot(['session_number', 'sort_order'])
            ->withTimestamps();
    }

    public function testDefinitions(): BelongsToMany
    {
        return $this->belongsToMany(TestDefinition::class, 'bimble_class_test')
            ->withPivot(['kind', 'sort_order'])
            ->withTimestamps();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ClassActivity::class)->latest('happened_at');
    }

    public function isTryOut(): bool
    {
        return $this->program_type === 'try_out';
    }
}
