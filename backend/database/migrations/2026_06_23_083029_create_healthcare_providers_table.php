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
        Schema::create('healthcare_providers', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('license_number', 50)->unique();

            $table->uuid('department_id');

            $table->uuid('hospital_id');

            $table->decimal('consultation_fee', 10, 2)
                ->default(0.00);
            $table->string('profile_picture')
                ->nullable();
            $table->integer('years_experience')
                ->nullable();

            $table->text('bio')
                ->nullable();

            $table->boolean('is_verified')
                ->default(false);

            $table->boolean('is_telehealth_available')
                ->default(false);

            $table->timestamps();

            $table->foreign('id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->restrictOnDelete();

            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->restrictOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('healthcare_providers');
    }
};