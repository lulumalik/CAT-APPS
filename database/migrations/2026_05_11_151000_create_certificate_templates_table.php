<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('program_category')->unique();
            $table->string('title')->default('Sertifikat Kelulusan');
            $table->string('subtitle')->nullable();
            $table->text('body_text')->nullable();
            $table->string('sign_name')->nullable();
            $table->string('sign_position')->nullable();
            $table->string('theme_color')->default('#1A1A1A');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
