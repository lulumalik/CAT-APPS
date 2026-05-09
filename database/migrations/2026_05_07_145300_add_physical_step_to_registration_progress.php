<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_progress', function (Blueprint $table) {
            $table->string('physical_status')->default('not_started')->after('psychology_admin_note');
            $table->json('physical_data')->nullable()->after('physical_status');
            $table->text('physical_admin_note')->nullable()->after('physical_data');
        });
    }

    public function down(): void
    {
        Schema::table('registration_progress', function (Blueprint $table) {
            $table->dropColumn(['physical_status', 'physical_data', 'physical_admin_note']);
        });
    }
};
