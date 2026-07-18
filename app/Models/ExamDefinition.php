<?php

namespace App\Models;

use App\Support\ShuffledQuestionOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'category', 'exam_track_id', 'duration', 'schedule_at', 'start_time', 'end_time', 'is_active', 'question_ids', 'created_by',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'schedule_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['status', 'time_until_start', 'is_locked', 'can_submit'];

    public function getStatusAttribute(): string
    {
        if (! $this->start_time || ! $this->end_time) {
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

    public function getIsLockedAttribute(): bool
    {
        return ! $this->start_time || Carbon::now()->lt($this->start_time);
    }

    public function getTimeUntilStartAttribute(): ?int
    {
        if (! $this->start_time) {
            return null;
        }

        $now = Carbon::now();
        if ($now->gte($this->start_time)) {
            return 0;
        }

        return $now->diffInSeconds($this->start_time);
    }

    public function canSubmit(): bool
    {
        if (! $this->start_time || ! $this->end_time) {
            return false;
        }

        $now = Carbon::now();

        return $now->gte($this->start_time) && $now->lte($this->end_time);
    }

    protected function getCanSubmitAttribute(): bool
    {
        return $this->canSubmit();
    }

    public function serializeForExam(array $extra = [], ?int $shuffleSeed = null): array
    {
        $data = $this->toArray();
        unset($data['question_ids']);

        $data['questions'] = ShuffledQuestionOrder::orderedQuestions(
            $this->question_ids ?? [],
            (int) $this->id,
            $shuffleSeed
        )
            ->map(fn (Question $question) => $question->makeHidden(['correct'])->toArray())
            ->values()
            ->all();

        return array_merge($data, $extra);
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }

    public function examTrack()
    {
        return $this->belongsTo(ExamTrack::class);
    }
}
