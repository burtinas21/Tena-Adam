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
        Schema::create('doctor_specializations', function (Blueprint $table) {
            $table->uuid('doctor_id');
            $table->uuid('specialization_id');

            $table->timestamps();

            $table->primary(['doctor_id', 'specialization_id']);

            $table->foreign('doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();

            $table->foreign('specialization_id')
                ->references('id')
                ->on('specializations')
                ->cascadeOnDelete();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('doctor_specializations');
    }
};