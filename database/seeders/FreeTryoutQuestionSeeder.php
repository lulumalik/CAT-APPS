<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\ArticleQuiz;
use App\Models\User;
use Illuminate\Support\Facades\File;

class FreeTryoutQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $articlesPath = database_path('seeders/article_quizzes.json');
        $jsonPath = database_path('seeders/free_tryout_questions.json');

        if (!File::exists($jsonPath)) {
            if (isset($this->command)) {
                $this->command->error("File free_tryout_questions.json tidak ditemukan!");
            }
            return;
        }

        $articleMap = [];
        if (File::exists($articlesPath)) {
            $articlesData = json_decode(File::get($articlesPath), true);
            foreach ($articlesData as $art) {
                $articleModel = ArticleQuiz::updateOrCreate(
                    ['title' => $art['title']],
                    [
                        'content' => $art['content'],
                        'created_by' => optional($admin)->id,
                    ]
                );
                $articleMap[$art['title']] = $articleModel->id;
            }
            if (isset($this->command)) {
                $this->command->info("Berhasil mengimpor " . count($articleMap) . " artikel bacaan!");
            }
        }

        $questionsData = json_decode(File::get($jsonPath), true);
        $count = 0;

        foreach ($questionsData as $item) {
            $articleId = null;
            if (!empty($item['article_quiz_title']) && isset($articleMap[$item['article_quiz_title']])) {
                $articleId = $articleMap[$item['article_quiz_title']];
            }

            Question::create([
                'question' => $item['question'],
                'category' => $item['category'],
                'difficulty' => $item['difficulty'],
                'type' => $item['type'],
                'options' => $item['options'],
                'correct' => $item['correct'],
                'image' => $item['image'] ?? null,
                'article_quiz_id' => $articleId,
                'batch' => $item['batch'] ?? 'Tryout 1',
                'created_by' => optional($admin)->id,
            ]);
            $count++;
        }

        if (isset($this->command)) {
            $this->command->info("Berhasil mengimpor {$count} soal dari free_tryout.pdf ke bank soal!");
        }
    }
}
