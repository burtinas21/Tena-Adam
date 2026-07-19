<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * chief_complaint, assessment, and diagnosis are required BEFORE completing
 * an encounter, but they are empty when the encounter is first created
 * (auto-created when the doctor calls the patient from the queue).
 * Making them nullable here removes the DB-level constraint so the encounter
 * can be created immediately, while the service-layer validation still
 * enforces they are filled in before the doctor marks it complete.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_encounters', function (Blueprint $table) {
            $table->text('chief_complaint')->nullable()->change();
            $table->text('assessment')->nullable()->change();
            $table->text('diagnosis')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medical_encounters', function (Blueprint $table) {
            // Restore NOT NULL (only safe if existing rows have values)
            $table->text('chief_complaint')->nullable(false)->change();
            $table->text('assessment')->nullable(false)->change();
            $table->text('diagnosis')->nullable(false)->change();
        });
    }
};
