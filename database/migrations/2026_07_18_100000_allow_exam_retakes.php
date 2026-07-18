<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'exam_definition_id']);
        });

        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->unsignedInteger('attempt_number')->default(1)->after('exam_definition_id');
            $table->index(['user_id', 'exam_definition_id', 'attempt_number']);
        });
    }

    public function down(): void
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'exam_definition_id', 'attempt_number']);
            $table->dropColumn('attempt_number');
        });

        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->unique(['user_id', 'exam_definition_id']);
        });
    }
};
