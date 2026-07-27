<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\File;

class FreeTryoutQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $jsonPath = database_path('seeders/free_tryout_questions.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("File free_tryout_questions.json tidak ditemukan!");
            return;
        }

        $questionsData = json_decode(File::get($jsonPath), true);
        $count = 0;

        foreach ($questionsData as $item) {
            Question::create([
                'question' => $item['question'],
                'category' => $item['category'],
                'difficulty' => $item['difficulty'],
                'type' => $item['type'],
                'options' => $item['options'],
                'correct' => $item['correct'],
                'image' => $item['image'] ?? null,
                'created_by' => optional($admin)->id,
            ]);
            $count++;
        }

        $this->command->info("Berhasil mengimpor {$count} soal dari free_tryout.pdf ke bank soal!");
    }
}
