<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand the status ENUM to include 'pending_payment'
        //    MySQL requires re-declaring the full ENUM list when modifying it.
        DB::statement("
            ALTER TABLE appointments
            MODIFY COLUMN status ENUM(
                'pending',
                'pending_payment',
                'confirmed',
                'cancelled',
                'completed',
                'no_show'
            ) NOT NULL DEFAULT 'pending'
        ");

        // 2. Add visit_type column if it doesn't exist yet
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'visit_type')) {
                $table->enum('visit_type', ['normal', 'follow_up', 'urgent'])
                    ->default('normal')
                    ->after('is_telehealth');
            }
        });
    }

    public function down(): void
    {
        // Remove visit_type
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'visit_type')) {
                $table->dropColumn('visit_type');
            }
        });

        // Revert status ENUM back to original values
        DB::statement("
            ALTER TABLE appointments
            MODIFY COLUMN status ENUM(
                'pending',
                'confirmed',
                'cancelled',
                'completed',
                'no_show'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
