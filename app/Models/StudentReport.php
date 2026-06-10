<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentReport extends Model
{
    public const TYPE_DAILY = 'daily';

    public const TYPE_WEEKLY = 'weekly_summary';

    protected $fillable = [
        'student_user_id',
        'bimble_class_id',
        'created_by',
        'type',
        'report_date',
        'period_start',
        'period_end',
        'title',
        'summary',
        'categories',
        'metrics',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
            'categories' => 'array',
            'metrics' => 'array',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function bimbleClass(): BelongsTo
    {
        return $this->belongsTo(BimbleClass::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
