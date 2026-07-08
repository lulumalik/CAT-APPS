<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BimbleClass;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BatchClassSyncService
{
    /**
     * Assign all eligible students from the given batches into the class.
     *
     * @param  iterable<int>|Collection  $batchIds
     * @return array{attached: int, skipped: int}
     */
    public function syncBatchesToClass(BimbleClass $bimbleClass, iterable $batchIds): array
    {
        $batchIds = collect($batchIds)->filter()->unique()->values();
        if ($batchIds->isEmpty()) {
            return ['attached' => 0, 'skipped' => 0];
        }

        $studentIds = User::query()
            ->where('role', 'user')
            ->whereHas('batches', fn ($q) => $q->whereIn('batches.id', $batchIds))
            ->pluck('id');

        return $this->attachStudentsToClass($bimbleClass, $studentIds);
    }

    /**
     * Assign a student into every class linked to the given batch.
     *
     * @return array{attached: int, skipped: int}
     */
    public function syncStudentToBatchClasses(Batch $batch, User $student): array
    {
        $classes = $batch->bimbleClasses()->get();
        $attached = 0;
        $skipped = 0;

        foreach ($classes as $class) {
            $result = $this->attachStudentsToClass($class, collect([$student->id]));
            $attached += $result['attached'];
            $skipped += $result['skipped'];
        }

        return compact('attached', 'skipped');
    }

    /**
     * @param  Collection<int, int>  $studentIds
     * @return array{attached: int, skipped: int}
     */
    public function attachStudentsToClass(BimbleClass $bimbleClass, Collection $studentIds): array
    {
        $attached = 0;
        $skipped = 0;
        $already = $bimbleClass->students()->pluck('users.id')->all();

        foreach ($studentIds->unique() as $studentId) {
            if (in_array((int) $studentId, array_map('intval', $already), true)) {
                $skipped++;

                continue;
            }

            $student = User::query()->find($studentId);
            if (! $student || $student->role !== 'user') {
                $skipped++;

                continue;
            }

            if (! $this->canInvite($student)) {
                $skipped++;

                continue;
            }

            $bimbleClass->students()->syncWithoutDetaching([
                $student->id => ['role' => 'student'],
            ]);

            UserNotification::create([
                'user_id' => $student->id,
                'type' => 'class_assigned',
                'title' => 'Kelas baru ditambahkan',
                'message' => sprintf('Anda ditambahkan ke kelas %s.', $bimbleClass->name),
                'payload' => [
                    'class_id' => $bimbleClass->id,
                    'class_name' => $bimbleClass->name,
                ],
            ]);

            $attached++;
            $already[] = $student->id;
        }

        return compact('attached', 'skipped');
    }

    private function canInvite(User $user): bool
    {
        if (User::isExamOnlyProgram($user->program_category)) {
            return false;
        }

        if ($user->role !== 'user') {
            return true;
        }

        if (empty($user->app_expires_at) || ! Carbon::parse($user->app_expires_at)->isPast()) {
            return true;
        }

        return $user->hasCompletedOnboarding();
    }
}
