<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_ratings', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('patient_id');

            $table->uuid('doctor_id');

            $table->uuid('appointment_id')
                ->unique();

            $table->unsignedTinyInteger('rating');

            $table->text('comment')
                ->nullable();

            $table->boolean('is_anonymous')
                ->default(false);

            $table->timestamps();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->cascadeOnDelete();

            $table->foreign('doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();

            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_ratings');
    }
};