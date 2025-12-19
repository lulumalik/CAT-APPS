<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TestDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','description','category','duration','schedule_at','start_time','end_time','is_active','question_ids','created_by'
    ];

    protected $casts = [
        'question_ids' => 'array',
        'schedule_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['status', 'time_until_start', 'is_locked', 'can_submit', 'questions'];

    /**
     * Get the status of the test (upcoming, ongoing, ended)
     */
    public function getStatusAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return 'scheduled';
        }

        $now = Carbon::now();

        if ($now->lt($this->start_time)) {
            return 'upcoming';
        }

        if ($now->gte($this->start_time) && $now->lte($this->end_time)) {
            return 'ongoing';
        }

        return 'ended';
    }

    /**
     * Check if the test is locked (not yet started)
     */
    public function getIsLockedAttribute(): bool
    {
        if (!$this->start_time) {
            return true;
        }

        return Carbon::now()->lt($this->start_time);
    }

    /**
     * Get the time remaining until the test starts (in seconds)
     */
    public function getTimeUntilStartAttribute(): ?int
    {
        if (!$this->start_time) {
            return null;
        }

        $now = Carbon::now();
        
        if ($now->gte($this->start_time)) {
            return 0;
        }

        return $now->diffInSeconds($this->start_time);
    }

    /**
     * Scope to get upcoming tests
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
                     ->where('start_time', '>', Carbon::now())
                     ->orderBy('start_time', 'asc');
    }

    /**
     * Scope to get ongoing tests
     */
    public function scopeOngoing($query)
    {
        return $query->where('is_active', true)
                     ->where('start_time', '<=', Carbon::now())
                     ->where('end_time', '>=', Carbon::now())
                     ->orderBy('start_time', 'asc');
    }

    /**
     * Scope to get available tests (upcoming or ongoing)
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                     ->orderBy('start_time', 'asc');
    }

    /**
     * Check if submissions are allowed (test is ongoing)
     */
    public function canSubmit(): bool
    {
        if (!$this->start_time || !$this->end_time) {
            return false;
        }

        $now = Carbon::now();
        return $now->gte($this->start_time) && $now->lte($this->end_time);
    }

    protected function getCanSubmitAttribute(): bool
    {
        return $this->canSubmit();
    }

    /**
     * Get the questions for this test
     */
    public function getQuestionsAttribute()
    {
        return Question::whereIn('id', $this->question_ids ?? [])->get();
    }

    /**
     * Get the submissions for this test
     */
    public function submissions()
    {
        return $this->hasMany(TestSubmission::class);
    }
}