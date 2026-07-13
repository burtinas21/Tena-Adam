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
        Schema::create('patients', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->date('date_of_birth')->nullable();

            $table->enum('gender', [
                'Male',
                'Female',
                'Other',
            ])->nullable();

            $table->text('address')->nullable();

            // $table->string('emergency_contact_name')->nullable();

            $table->string('blood_type', 5)->nullable();

            $table->text('allergies')->nullable();

            $table->text('medical_history')->nullable();

            $table->string('national_id', 20)->nullable();

            $table->string('occupation')->nullable();

            $table->enum('patient_status', [
                'pending',
                'active',
                'inactive',
                'suspended',
            ])->default('pending');

            $table->uuid('registered_by')->nullable();

            $table->timestamps();

            $table->foreign('id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('registered_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
