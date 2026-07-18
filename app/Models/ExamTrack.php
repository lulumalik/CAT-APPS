<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamTrack extends Model
{
    protected $fillable = [
        'exam_category_id', 'name', 'slug', 'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $track): void {
            if (blank($track->slug)) {
                $track->slug = static::generateUniqueSlug($track->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'track';
        $slug = $base;
        $counter = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $counter++;
            $slug = "{$base}-{$counter}";
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(ExamCategory::class, 'exam_category_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function testDefinitions()
    {
        return $this->hasMany(TestDefinition::class);
    }

    public function examDefinitions()
    {
        return $this->hasMany(ExamDefinition::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
