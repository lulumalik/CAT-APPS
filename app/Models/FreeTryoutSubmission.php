<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeTryoutSubmission extends Model
{
    protected $fillable = [
        'test_definition_id',
        'full_name',
        'gender',
        'city',
        'birth_date',
        'phone',
        'answers',
        'score',
        'total_questions',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'birth_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function testDefinition()
    {
        return $this->belongsTo(TestDefinition::class);
    }
}
