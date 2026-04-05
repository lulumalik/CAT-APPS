<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'content',
        'created_by',
    ];

    public function comments()
    {
        return $this->hasMany(DiscussionComment::class, 'thread_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
