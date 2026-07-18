<?php

namespace Database\Seeders;

use App\Models\ExamDefinition;
use App\Models\ExamTrack;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamDefinitionSeeder extends Seeder
{
    /**
     * Satu paket simulasi ujian aktif per track memakai 10 soal seed masing-masing.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        foreach (ExamTrack::where('is_active', true)->get() as $track) {
            $questionIds = Question::where('exam_track_id', $track->id)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            if (empty($questionIds)) {
                continue;
            }

            ExamDefinition::create([
                'name' => "Simulasi {$track->name} — Paket 1",
                'description' => "Paket simulasi ujian {$track->name} dengan ".count($questionIds).' soal pilihan ganda.',
                'category' => $track->name,
                'exam_track_id' => $track->id,
                'duration' => 30,
                'schedule_at' => now(),
                'start_time' => now(),
                'end_time' => now()->addMonths(6),
                'is_active' => true,
                'question_ids' => $questionIds,
                'created_by' => optional($admin)->id,
            ]);
        }
    }
}
