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
        Schema::create('medical_encounters', function (Blueprint $table) {

            // Primary Key (UUID)
            $table->uuid('id')->primary();

            // Relationships
            $table->foreignUuid('patient_id')
                ->constrained('patients')
                ->cascadeOnDelete();

            $table->foreignUuid('doctor_id')
                ->constrained('healthcare_providers')
                ->cascadeOnDelete();

            $table->foreignUuid('hospital_id')
                ->constrained('hospitals')
                ->cascadeOnDelete();

            $table->foreignUuid('appointment_id')
                ->nullable()
                ->constrained('appointments')
                ->nullOnDelete();
          
            // Consultation Information
            $table->dateTime('encounter_date');

            $table->text('chief_complaint')->nullable();

            $table->text('history')->nullable();

            $table->text('physical_exam')->nullable();

            $table->text('assessment')->nullable();

            $table->text('diagnosis')->nullable();

            $table->string('diagnosis_icd10', 20)->nullable();

            $table->text('treatment_plan')->nullable();

            $table->text('clinical_notes')->nullable();

            $table->date('follow_up_date')->nullable();

            // Consultation Status
            $table->enum('status', [
                'in_progress',
                'completed',
                'cancelled',
            ])->default('in_progress');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_encounters');
    }
};