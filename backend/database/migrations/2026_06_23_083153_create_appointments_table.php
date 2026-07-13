<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('slot_id')->nullable();

            $table->uuid('patient_id');

            $table->uuid('doctor_id');

            $table->uuid('hospital_id');

            $table->uuid('department_id')->nullable();

            $table->dateTime('scheduled_time');

            $table->integer('duration_min')->default(30);

            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed',
                'no_show'
            ])->default('pending');

            $table->text('reason');

            $table->text('notes')->nullable();

            $table->boolean('is_telehealth')->default(false);

            $table->timestamp('cancelled_at')->nullable();

            $table->uuid('approved_by')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['doctor_id', 'scheduled_time']);

            // FK relationships

            $table->foreign('slot_id')
                ->references('id')
                ->on('appointment_slots')
                ->nullOnDelete();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->cascadeOnDelete();

            $table->foreign('doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();

            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->cascadeOnDelete();

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unique([
                'doctor_id',
                'scheduled_time'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};