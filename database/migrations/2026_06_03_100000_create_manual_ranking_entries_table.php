<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_ranking_entries', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 20);
            $table->string('group_id', 64);
            $table->string('subcategory_id', 64);
            $table->foreignId('bimble_class_id')->nullable()->constrained('bimble_classes')->nullOnDelete();
            $table->string('cohort', 120)->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 12, 4);
            $table->string('unit', 32)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('context_key', 255);
            $table->timestamps();

            $table->unique('context_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_ranking_entries');
    }
};
