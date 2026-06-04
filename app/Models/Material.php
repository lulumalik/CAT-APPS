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

        if (in_array($user->role, ['admin', 'mentor'], true)) {
            return true;
        }

        return $this->bimbleClasses()->whereHas('students', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->exists();
    }

    public function scopePublishedForBlog($query, ?\App\Models\User $user)
    {
        $query->where('status', 'published');

        if ($user && in_array($user->role, ['admin', 'mentor'], true)) {
            return $query;
        }

        if ($user) {
            return $query->where(function ($builder) use ($user) {
                $builder->where('visibility', 'public')
                    ->orWhere(function ($restricted) use ($user) {
                        $restricted->where('visibility', 'class_only')
                            ->whereHas('bimbleClasses', function ($classQuery) use ($user) {
                                $classQuery->whereHas('students', function ($studentQuery) use ($user) {
                                    $studentQuery->where('users.id', $user->id);
                                });
                            });
                    });
            });
        }

        return $query->where('visibility', 'public');
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
