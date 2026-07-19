<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The medical_documents table was created with $table->id() (auto-increment bigint)
 * instead of $table->uuid('id')->primary(). The model uses HasUuids, so MySQL
 * truncates the UUID string to 0. This migration converts the PK to CHAR(36).
 *
 * MySQL requires removing AUTO_INCREMENT before the PK can be dropped, so we
 * do it all in raw SQL to have full control over the sequence.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Remove AUTO_INCREMENT (keep column, just change to plain BIGINT)
        DB::statement('ALTER TABLE medical_documents MODIFY id BIGINT NOT NULL');

        // Step 2: Drop the primary key now that AUTO_INCREMENT is gone
        DB::statement('ALTER TABLE medical_documents DROP PRIMARY KEY');

        // Step 3: Change the column type to CHAR(36) for UUIDs
        DB::statement('ALTER TABLE medical_documents MODIFY id CHAR(36) NOT NULL');

        // Step 4: Re-add as primary key
        DB::statement('ALTER TABLE medical_documents ADD PRIMARY KEY (id)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE medical_documents DROP PRIMARY KEY');
        DB::statement('ALTER TABLE medical_documents MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        DB::statement('ALTER TABLE medical_documents ADD PRIMARY KEY (id)');
    }
};
