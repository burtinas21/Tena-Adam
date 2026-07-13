<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vitals', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('encounter_id');
            $table->uuid('patient_id');

            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('bmi', 5, 2)->nullable();
            $table->integer('blood_oxygen')->nullable();

            $table->dateTime('measured_at')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('encounter_id')
                  ->references('id')
                  ->on('medical_encounters')
                  ->onDelete('cascade');

            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('vitals');
    }
};
