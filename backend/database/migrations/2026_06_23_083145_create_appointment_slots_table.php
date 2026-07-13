<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_slots', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('doctor_id');

            $table->dateTime('start_time');

            $table->dateTime('end_time');

            $table->enum('status', [
                'available',
                'booked',
                'blocked',
                'completed',
                'cancelled'
            ])->default('available');

            $table->timestamps();

            $table->unique(['doctor_id', 'start_time']);

            $table->foreign('doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
    }
};