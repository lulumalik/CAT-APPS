<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualRankingEntry extends Model
{
    protected $fillable = [
        'scope',
        'group_id',
        'subcategory_id',
        'bimble_class_id',
        'cohort',
        'user_id',
        'score',
        'unit',
        'notes',
        'score_date',
        'created_by',
        'context_key',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'score_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bimbleClass(): BelongsTo
    {
        return $this->belongsTo(BimbleClass::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function buildContextKey(array $data): string
    {
        $scoreDate = $data['score_date'] ?? '';
        if ($scoreDate instanceof \DateTimeInterface) {
            $scoreDate = $scoreDate->format('Y-m-d');
        }

        return implode('|', [
            $data['scope'] ?? '',
            $data['group_id'] ?? '',
            $data['subcategory_id'] ?? '',
            (string) ($data['bimble_class_id'] ?? ''),
            (string) ($data['cohort'] ?? ''),
            (string) ($data['user_id'] ?? ''),
            (string) $scoreDate,
        ]);
    }
}
