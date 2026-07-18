<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question','category','exam_track_id','difficulty','options','correct','created_by', 'type', 'image'
    ];

    protected $casts = [
        'options' => 'array',
        'schedule_at' => 'datetime',
    ];

    public function examTrack()
    {
        return $this->belongsTo(ExamTrack::class);
    }
}