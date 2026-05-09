<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimble_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class_code', 32)->unique();
            $table->string('instructor_name')->nullable();
            $table->string('academic_period')->nullable();
            $table->unsignedSmallInteger('participant_count')->nullable();
            $table->string('program_type')->default('regular_online');
            $table->string('cover_image_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bimble_class_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bimble_class_id')->constrained('bimble_classes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('student');
            $table->timestamps();
            $table->unique(['bimble_class_id', 'user_id']);
        });

        Schema::create('bimble_class_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bimble_class_id')->constrained('bimble_classes')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->unsignedSmallInteger('session_number')->default(1);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['bimble_class_id', 'material_id']);
        });

        Schema::create('bimble_class_test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bimble_class_id')->constrained('bimble_classes')->cascadeOnDelete();
            $table->foreignId('test_definition_id')->constrained('test_definitions')->cascadeOnDelete();
            $table->string('kind')->default('cbt');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['bimble_class_id', 'test_definition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimble_class_test');
        Schema::dropIfExists('bimble_class_material');
        Schema::dropIfExists('bimble_class_user');
        Schema::dropIfExists('bimble_classes');
    }
};
