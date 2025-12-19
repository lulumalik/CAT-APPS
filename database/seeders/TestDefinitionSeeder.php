<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestDefinition;
use App\Models\Question;
use App\Models\User;

class TestDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email','admin@example.com')->first();
        $ids = Question::pluck('id')->all();

        if (!$ids) return;

        $tests = [
            [
                'name' => 'General Knowledge Quick Test',
                'description' => 'Mixed categories quick assessment',
                'category' => 'Mixed',
                'duration' => 30,
                'schedule_at' => now()->addDay(),
                'start_time' => now()->addMinutes(2), // Starts in 2 minutes for testing
                'end_time' => now()->addMinutes(2)->addHours(1), // 1 hour duration
                'is_active' => true,
                'question_ids' => array_slice($ids, 0, min(3, count($ids))),
            ],
            [
                'name' => 'Science Focus Test',
                'description' => 'Science questions',
                'category' => 'Science',
                'duration' => 45,
                'schedule_at' => now()->addDays(3),
                'start_time' => now()->addDay(),
                'end_time' => now()->addDay()->addHours(2), // 2 hours duration
                'is_active' => true,
                'question_ids' => $ids,
            ],
            [
                'name' => 'Mathematics Challenge',
                'description' => 'Advanced math problems',
                'category' => 'Math',
                'duration' => 60,
                'schedule_at' => now()->addDays(5),
                'start_time' => now()->addDays(5),
                'end_time' => now()->addDays(5)->addHours(3), // 3 hours duration
                'is_active' => true,
                'question_ids' => array_slice($ids, 0, min(5, count($ids))),
            ],
        ];

        foreach ($tests as $t) {
            TestDefinition::create($t + ['created_by' => optional($admin)->id]);
        }
    }
}