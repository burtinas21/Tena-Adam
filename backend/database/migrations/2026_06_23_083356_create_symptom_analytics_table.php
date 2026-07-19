<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('symptom_analytics', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('symptom_id');
        $table->uuid('recommended_department_id');
        $table->boolean('selected_by_patient')->default(false);
        $table->uuid('patient_id')->nullable();
        $table->uuid('session_id')->nullable(); // tracking session
        $table->timestamps();

        $table->foreign('symptom_id')->references('id')->on('symptoms')->onDelete('cascade');
        $table->foreign('recommended_department_id')->references('id')->on('departments')->onDelete('cascade');
        $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptom_analytics');
    }
};
