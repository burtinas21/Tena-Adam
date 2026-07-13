<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('medications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name', 255);
            $table->string('generic_name', 255)->nullable();
            $table->string('manufacturer', 255)->nullable();
            $table->string('dosage_form', 50);
            $table->string('strength', 50)->nullable();
            $table->string('category', 100)->nullable();
            $table->boolean('requires_prescription')->default(true);
            $table->text('side_effects')->nullable();
            $table->text('interactions')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('medications');
    }
};
