<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('exam_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_category_id')->constrained('exam_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->index()->after('remember_token');
            $table->string('avatar_url')->nullable()->after('google_id');
            $table->foreignId('exam_category_id')->nullable()->after('program_category')
                ->constrained('exam_categories')->nullOnDelete();
            $table->foreignId('exam_track_id')->nullable()->after('exam_category_id')
                ->constrained('exam_tracks')->nullOnDelete();
        });

        foreach (['questions', 'test_definitions', 'exam_definitions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('exam_track_id')->nullable()->after('category')
                    ->constrained('exam_tracks')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['questions', 'test_definitions', 'exam_definitions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('exam_track_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('exam_track_id');
            $table->dropConstrainedForeignId('exam_category_id');
            $table->dropColumn(['google_id', 'avatar_url']);
        });

        Schema::dropIfExists('exam_tracks');
        Schema::dropIfExists('exam_categories');
    }
};
