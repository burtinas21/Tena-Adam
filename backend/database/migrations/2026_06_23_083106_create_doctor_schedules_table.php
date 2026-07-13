<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('doctor_id');

            $table->tinyInteger('day_of_week');

            $table->time('start_time');

            $table->time('end_time');

            $table->integer('slot_duration_min')
                ->default(30);

            $table->time('lunch_start')
                ->nullable();

            $table->time('lunch_end')
                ->nullable();

            $table->boolean('is_available')
                ->default(true);

            $table->timestamps();

            $table->foreign('doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();

            $table->unique([
                'doctor_id',
                'day_of_week'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};