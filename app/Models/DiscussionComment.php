<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'parent_id',
        'content',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function thread()
    {
        return $this->belongsTo(DiscussionThread::class, 'thread_id');
    }
}
