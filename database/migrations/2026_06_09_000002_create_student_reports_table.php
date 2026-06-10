<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bimble_class_id')->nullable()->constrained('bimble_classes')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20)->default('daily'); // daily | weekly_summary
            $table->date('report_date');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->json('categories')->nullable(); // {akademik, jasmani, kedisiplinan, ...}
            $table->json('metrics')->nullable();     // {materi_progress, tes_rata, jasmani_rata, ...}
            $table->timestamps();

            $table->index(['student_user_id', 'type', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
