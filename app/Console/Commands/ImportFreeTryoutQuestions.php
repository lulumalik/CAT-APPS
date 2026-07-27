<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\FreeTryoutQuestionSeeder;

class ImportFreeTryoutQuestions extends Command
{
    protected $signature = 'import:free-tryout';
    protected $description = 'Import 90 tryout questions from free_tryout.pdf into question bank';

    public function handle()
    {
        $this->info('Mengimpor 90 soal dari free_tryout.pdf...');
        $seeder = new FreeTryoutQuestionSeeder();
        $seeder->setCommand($this);
        $seeder->run();
        return 0;
    }
}
