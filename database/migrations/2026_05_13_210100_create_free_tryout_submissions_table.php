<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_tryout_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_definition_id')->constrained('test_definitions')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('gender', 20);
            $table->string('city');
            $table->date('birth_date');
            $table->string('phone', 32);
            $table->json('answers');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index(['test_definition_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_tryout_submissions');
    }
};
