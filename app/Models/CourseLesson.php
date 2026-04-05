<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'lesson_type',
        'content',
        'file_path',
        'video_url',
        'live_url',
        'test_definition_id',
        'sort_order',
        'is_preview',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function quizTest()
    {
        return $this->belongsTo(TestDefinition::class, 'test_definition_id');
    }
}
