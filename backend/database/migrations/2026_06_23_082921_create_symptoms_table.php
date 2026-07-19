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
    Schema::create('symptoms', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name', 100)->unique(); // e.g. Fever
        $table->text('description')->nullable();
        $table->string('category', 50)->nullable(); // e.g. General, Respiratory
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptoms');
    }
};
