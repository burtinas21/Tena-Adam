<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Temporarily change the column to VARCHAR so we can store any value
        DB::statement("ALTER TABLE appointments MODIFY COLUMN visit_type VARCHAR(20) NOT NULL DEFAULT 'normal'");

        // Step 2: Migrate old values to new ones
        DB::statement("UPDATE appointments SET visit_type = 'normal' WHERE visit_type IN ('in_person', 'telehealth')");

        // Step 3: Now change to the new ENUM with the correct values
        DB::statement("
            ALTER TABLE appointments
            MODIFY COLUMN visit_type ENUM('normal', 'follow_up', 'urgent')
            NOT NULL DEFAULT 'normal'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Temporarily use VARCHAR to allow data migration
        DB::statement("ALTER TABLE appointments MODIFY COLUMN visit_type VARCHAR(20) NOT NULL DEFAULT 'in_person'");

        // Revert data: map back to original values
        DB::statement("UPDATE appointments SET visit_type = 'in_person' WHERE visit_type IN ('normal', 'follow_up', 'urgent')");

        // Revert to the old ENUM
        DB::statement("
            ALTER TABLE appointments
            MODIFY COLUMN visit_type ENUM('in_person', 'telehealth')
            NOT NULL DEFAULT 'in_person'
        ");
    }
};
