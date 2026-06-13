<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('manual_ranking_entries')) {
            return;
        }

        Schema::table('manual_ranking_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('manual_ranking_entries', 'score_date')) {
                $table->date('score_date')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('manual_ranking_entries')) {
            return;
        }

        Schema::table('manual_ranking_entries', function (Blueprint $table) {
            if (Schema::hasColumn('manual_ranking_entries', 'score_date')) {
                $table->dropColumn('score_date');
            }
        });
    }
};
