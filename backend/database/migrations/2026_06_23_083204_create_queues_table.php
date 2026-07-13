<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('appointment_id')->nullable();

            $table->uuid('doctor_id');

            $table->uuid('hospital_id');

            $table->date('queue_date');

            $table->integer('queue_number');

            $table->enum('status', [
                'waiting',
                'in_consultation',
                'completed',
                'skipped',
                'no_show'
            ])->default('waiting');

            $table->timestamp('called_at')->nullable();

            $table->timestamp('started_at')->nullable();

            $table->timestamp('ended_at')->nullable();

            $table->string('walk_in_patient_name')->nullable();

            $table->string('walk_in_phone')->nullable();

            $table->timestamps();

            $table->unique(['doctor_id', 'queue_date', 'queue_number']);

            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->nullOnDelete();

            $table->foreign('doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();

            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue');
    }
};