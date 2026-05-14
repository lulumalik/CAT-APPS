<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $activeIds = DB::table('test_definitions')
            ->where('is_free_tryout', true)
            ->orderByDesc('updated_at')
            ->pluck('id')
            ->all();

        if (count($activeIds) <= 1) {
            return;
        }

        $keepId = $activeIds[0];
        DB::table('test_definitions')
            ->where('is_free_tryout', true)
            ->where('id', '!=', $keepId)
            ->update(['is_free_tryout' => false]);
    }

    public function down(): void
    {
        // no-op
    }
};
