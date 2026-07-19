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
    Schema::create('symptom_department_mappings', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('symptom_id');
        $table->uuid('department_id');
        $table->integer('relevance_score')->default(50); // 0–100
        $table->boolean('is_primary')->default(false);
        $table->enum('evidence_level', ['high', 'medium', 'low'])->default('medium');
        $table->timestamps();

        $table->unique(['symptom_id', 'department_id']);

        $table->foreign('symptom_id')->references('id')->on('symptoms')->onDelete('cascade');
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptom_department_mappings');
    }
};
