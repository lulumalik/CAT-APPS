<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'status',
        'visibility',
        'created_by'
    ];

    public function bimbleClasses()
    {
        return $this->belongsToMany(BimbleClass::class, 'bimble_class_material')
            ->withPivot(['session_number', 'sort_order'])
            ->withTimestamps();
    }

    public function canBeViewedBy(?\App\Models\User $user): bool
    {
        if ($this->visibility === 'public') {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $this->bimbleClasses()->whereHas('students', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if (empty($material->slug)) {
                $material->slug = Str::slug($material->title) . '-' . uniqid();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
