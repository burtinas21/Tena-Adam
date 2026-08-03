<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Walk-in patients do not have a registered Patient record, so patient_id
 * must be nullable.  We also store the walk-in name/phone directly on the
 * encounter so the doctor can identify the patient in the Medical Encounters
 * worklist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_encounters', function (Blueprint $table) {
            // Make patient_id nullable so walk-in encounters can be created
            // without a registered patient record.
            $table->foreignUuid('patient_id')->nullable()->change();

            // Walk-in patient details (populated when appointment_id is null)
            $table->string('walk_in_patient_name', 255)->nullable()->after('appointment_id');
            $table->string('walk_in_phone', 20)->nullable()->after('walk_in_patient_name');
        });
    }

    public function down(): void
    {
        Schema::table('medical_encounters', function (Blueprint $table) {
            $table->dropColumn(['walk_in_patient_name', 'walk_in_phone']);
            $table->foreignUuid('patient_id')->nullable(false)->change();
        });
    }
};
