<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('current_step')->default('administration');

            $table->string('administration_status')->default('not_started');
            $table->json('administration_data')->nullable();
            $table->text('administration_admin_note')->nullable();

            $table->string('health_status')->default('not_started');
            $table->json('health_data')->nullable();
            $table->text('health_admin_note')->nullable();

            $table->string('psychology_status')->default('not_started');
            $table->json('psychology_data')->nullable();
            $table->text('psychology_admin_note')->nullable();

            $table->boolean('fully_completed')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_progress');
    }
};
