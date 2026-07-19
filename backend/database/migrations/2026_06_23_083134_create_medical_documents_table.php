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
        Schema::create('medical_documents', function (Blueprint $table) {

            $table->uuid('id')->primary();

            // Foreign keys (patient_id, encounter_id, uploaded_by) are added in
            // 2026_07_16_085656_add_columns_to_medical_documents_table.php
            // because medical_encounters is created AFTER this migration runs.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_documents');
    }
};