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
        Schema::create('hospitals', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('name', 255)->unique();

            $table->string('code', 20)->unique()->nullable();

            $table->text('address');

            $table->string('city', 100);

            $table->string('region', 100)->nullable();

            $table->string('phone', 20)->nullable();

            $table->string('email')->nullable();

            $table->string('website')->nullable();

            $table->string('logo_url')->nullable();

            $table->boolean('is_active')->default(true);

            $table->string('registration_number', 50)->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};