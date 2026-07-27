<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleQuiz extends Model
{
    use HasFactory;

    protected $table = 'article_quizzes';

    protected $fillable = [
        'title',
        'content',
        'created_by',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'article_quiz_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
