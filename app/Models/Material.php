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
        'created_by'
    ];

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
