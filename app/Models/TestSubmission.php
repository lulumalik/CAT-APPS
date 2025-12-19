<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'test_definition_id',
        'answers',
        'score',
        'submitted_at'
    ];

    protected $casts = [
        'answers' => 'array',
        'submitted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testDefinition()
    {
        return $this->belongsTo(TestDefinition::class);
    }
}
