<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add cancel_reason to appointments (was in request rules but missing from table)
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('notes');
            }
        });

        // 2. Add appointment_id to medical_documents so files can be attached
        //    at booking time (before a medical encounter exists)
        Schema::table('medical_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('medical_documents', 'appointment_id')) {
                $table->uuid('appointment_id')->nullable()->after('encounter_id');
                $table->foreign('appointment_id')
                    ->references('id')
                    ->on('appointments')
                    ->nullOnDelete();
            }
        });

        // 3. Create appointment_referrals table
        Schema::create('appointment_referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // The original appointment being referred
            $table->uuid('appointment_id');

            // The doctor who referred the patient
            $table->uuid('referred_by');   // healthcare_providers.id

            // The doctor the patient is referred TO (nullable = department-only referral)
            $table->uuid('referred_to_doctor_id')->nullable();

            // Optionally refer to a different department
            $table->uuid('referred_to_department_id')->nullable();

            $table->text('reason');

            // pending = new doctor hasn't acted yet
            // accepted = new doctor confirmed the referred appointment
            // rejected = new doctor rejected
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');

            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->cascadeOnDelete();

            $table->foreign('referred_by')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();

            $table->foreign('referred_to_doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->nullOnDelete();

            $table->foreign('referred_to_department_id')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_referrals');

        Schema::table('medical_documents', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
            $table->dropColumn('appointment_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('cancel_reason');
        });
    }
};
