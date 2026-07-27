<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question','category','difficulty','options','correct','created_by', 'type', 'image', 'article_quiz_id'
    ];

    protected $casts = [
        'options' => 'array',
        'schedule_at' => 'datetime',
    ];

    public function articleQuiz()
    {
        return $this->belongsTo(ArticleQuiz::class, 'article_quiz_id');
    }
}