<?php

namespace Database\Seeders;

use App\Models\ExamDefinition;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed aplikasi CATLab (taksonomi ujian + soal demo).
     * User admin dipertahankan seperti sebelumnya.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'username' => 'admin',
                'email_verified_at' => now(),
                'program_category' => User::PROGRAM_REGULAR,
            ]
        );

        $this->call([ExamCategorySeeder::class]);

        // Isi soal & paket ujian hanya jika masih kosong (aman dijalankan ulang).
        if (Question::count() === 0) {
            $this->call([QuestionSeeder::class]);
        }

        if (ExamDefinition::count() === 0) {
            $this->call([ExamDefinitionSeeder::class]);
        }
    }
}
