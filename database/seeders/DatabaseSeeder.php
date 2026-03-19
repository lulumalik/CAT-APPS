<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use App\Models\TestDefinition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        if (Question::count() === 0) {
            $this->call([QuestionSeeder::class]);
        }

        if (TestDefinition::count() === 0) {
            $this->call([TestDefinitionSeeder::class]);
        }
    }
}
