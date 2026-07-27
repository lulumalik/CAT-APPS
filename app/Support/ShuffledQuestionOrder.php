<?php

namespace App\Support;

use App\Models\Question;
use Illuminate\Support\Collection;

class ShuffledQuestionOrder
{
    /**
     * Return question IDs in a stable pseudo-random order per participant.
     */
    public static function shuffleIds(array $questionIds, int $seed, int $definitionId): array
    {
        $ids = array_values(array_filter($questionIds, fn ($id) => $id !== null && $id !== ''));

        if (count($ids) <= 1) {
            return $ids;
        }

        usort($ids, function ($a, $b) use ($seed, $definitionId) {
            $hashA = crc32("{$seed}:{$definitionId}:{$a}");
            $hashB = crc32("{$seed}:{$definitionId}:{$b}");

            return $hashA <=> $hashB;
        });

        return $ids;
    }

    /**
     * Load questions preserving admin order, or shuffled order when $shuffleSeed is set.
     */
    public static function orderedQuestions(array $questionIds, int $definitionId, ?int $shuffleSeed = null): Collection
    {
        $ids = array_values(array_filter($questionIds, fn ($id) => $id !== null && $id !== ''));

        if ($ids === []) {
            return collect();
        }

        if ($shuffleSeed !== null) {
            $ids = self::shuffleIds($ids, $shuffleSeed, $definitionId);
        }

        $byId = Question::with('articleQuiz')->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $byId->get($id))
            ->filter()
            ->values();
    }
}
