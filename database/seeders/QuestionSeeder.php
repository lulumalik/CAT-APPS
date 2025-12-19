<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\User;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email','admin@example.com')->first();

        $questions = [
            [
                'question' => 'What is the capital of France?',
                'category' => 'Geography',
                'difficulty' => 'Easy',
                'options' => [
                    ['key'=>'A','label'=>'London'],
                    ['key'=>'B','label'=>'Berlin'],
                    ['key'=>'C','label'=>'Paris'],
                    ['key'=>'D','label'=>'Madrid'],
                ],
                'correct' => 'C',
            ],
            [
                'question' => '2 + 2 = ?',
                'category' => 'Math',
                'difficulty' => 'Easy',
                'options' => [
                    ['key'=>'A','label'=>'3'],
                    ['key'=>'B','label'=>'4'],
                    ['key'=>'C','label'=>'5'],
                    ['key'=>'D','label'=>'6'],
                ],
                'correct' => 'B',
            ],
            [
                'question' => 'Water chemical formula?',
                'category' => 'Science',
                'difficulty' => 'Medium',
                'options' => [
                    ['key'=>'A','label'=>'CO2'],
                    ['key'=>'B','label'=>'H2O'],
                    ['key'=>'C','label'=>'O2'],
                    ['key'=>'D','label'=>'NaCl'],
                ],
                'correct' => 'B',
            ],
            [
                'question' => 'Year World War II ended?',
                'category' => 'History',
                'difficulty' => 'Medium',
                'options' => [
                    ['key'=>'A','label'=>'1945'],
                    ['key'=>'B','label'=>'1939'],
                    ['key'=>'C','label'=>'1918'],
                    ['key'=>'D','label'=>'1950'],
                ],
                'correct' => 'A',
            ],
            [
                'question' => 'HTTP stands for?',
                'category' => 'IT',
                'difficulty' => 'Easy',
                'options' => [
                    ['key'=>'A','label'=>'HyperText Transfer Protocol'],
                    ['key'=>'B','label'=>'High Transfer Text Protocol'],
                    ['key'=>'C','label'=>'Hyperlink Text Transport Process'],
                    ['key'=>'D','label'=>'Host Transfer Transport Protocol'],
                ],
                'correct' => 'A',
            ],
        ];

        foreach ($questions as $q) {
            Question::create($q + ['created_by' => optional($admin)->id]);
        }
    }
}