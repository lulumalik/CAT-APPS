<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bimble_classes', function (Blueprint $table) {
            if (! Schema::hasColumn('bimble_classes', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('instructor_name')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('bimble_classes', 'academic_period_start')) {
                $table->date('academic_period_start')->nullable()->after('academic_period');
            }

            if (! Schema::hasColumn('bimble_classes', 'academic_period_end')) {
                $table->date('academic_period_end')->nullable()->after('academic_period_start');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bimble_classes', function (Blueprint $table) {
            if (Schema::hasColumn('bimble_classes', 'instructor_id')) {
                $table->dropConstrainedForeignId('instructor_id');
            }

            if (Schema::hasColumn('bimble_classes', 'academic_period_start')) {
                $table->dropColumn('academic_period_start');
            }

            if (Schema::hasColumn('bimble_classes', 'academic_period_end')) {
                $table->dropColumn('academic_period_end');
            }
        });
    }
};
