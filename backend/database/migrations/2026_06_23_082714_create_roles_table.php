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
        Schema::create('roles', function (Blueprint $table) {

            $table->uuid('id')->primary();

            // hospital_id scopes a role to a specific hospital (null = global role)
            $table->uuid('hospital_id')->nullable()->index();

            $table->string('name', 50);

            $table->text('description')->nullable();

            $table->boolean('is_default')->default(false);

            $table->timestamps();

            // A role name must be unique within its scope (global or per-hospital)
            $table->unique(['name', 'hospital_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
