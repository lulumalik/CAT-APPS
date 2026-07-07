<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_progress', function (Blueprint $table) {
            $table->boolean('payment_confirmed')->default(false)->after('fully_completed');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_confirmed');
        });
    }

    public function down(): void
    {
        Schema::table('registration_progress', function (Blueprint $table) {
            $table->dropColumn(['payment_confirmed', 'payment_confirmed_at']);
        });
    }
};
