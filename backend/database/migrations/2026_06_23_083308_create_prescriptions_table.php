<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('encounter_id');
            $table->uuid('medication_id')->nullable();
            $table->string('medication_name', 255);
            $table->string('dosage', 50);
            $table->string('frequency', 100);
            $table->string('route', 50)->nullable();
            $table->integer('duration_days')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('refills')->default(0);
            $table->enum('status', ['active','completed','cancelled'])->default('active');

            $table->timestamps();

            // Foreign keys
            $table->foreign('encounter_id')->references('id')->on('medical_encounters')->onDelete('cascade');
            $table->foreign('medication_id')->references('id')->on('medications')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('prescriptions');
    }
};
