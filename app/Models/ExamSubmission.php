<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'exam_definition_id',
        'attempt_number',
        'answers',
        'score',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'attempt_number' => 'integer',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examDefinition()
    {
        return $this->belongsTo(ExamDefinition::class);
    }
}
