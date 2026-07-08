<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('batches')) {
            Schema::create('batches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable()->unique();
                $table->date('starts_on')->nullable();
                $table->date('ends_on')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('batch_user')) {
            Schema::create('batch_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['batch_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('batch_bimble_class')) {
            Schema::create('batch_bimble_class', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
                $table->foreignId('bimble_class_id')->constrained('bimble_classes')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['batch_id', 'bimble_class_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_bimble_class');
        Schema::dropIfExists('batch_user');
        Schema::dropIfExists('batches');
    }
};
