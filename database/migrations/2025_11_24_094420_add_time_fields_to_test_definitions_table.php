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
        Schema::table('test_definitions', function (Blueprint $table) {
            $table->dateTime('start_time')->nullable()->after('schedule_at');
            $table->dateTime('end_time')->nullable()->after('start_time');
            $table->boolean('is_active')->default(true)->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_definitions', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'is_active']);
        });
    }
};
