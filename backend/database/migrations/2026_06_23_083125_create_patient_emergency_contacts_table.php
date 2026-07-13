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
 Schema::create('patient_emergency_contacts', function (Blueprint $table) {

    $table->uuid('id')->primary();

    $table->uuid('patient_id');

    $table->string('name');

    $table->string('relationship', 50);

    $table->string('phone', 20);

    $table->string('email')->nullable();

    $table->text('address')->nullable();

    $table->boolean('is_primary')->default(false);

    $table->timestamps();

    $table->foreign('patient_id')
        ->references('id')
        ->on('patients')
        ->cascadeOnDelete();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_emergency_contacts');
    }
};
