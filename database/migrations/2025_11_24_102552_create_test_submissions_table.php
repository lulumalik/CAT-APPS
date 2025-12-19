<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_definition_id')->constrained()->onDelete('cascade');
            $table->json('answers');
            $table->integer('score')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            // Mencegah user submit test yang sama lebih dari sekali
            $table->unique(['user_id', 'test_definition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_submissions');
    }
};
