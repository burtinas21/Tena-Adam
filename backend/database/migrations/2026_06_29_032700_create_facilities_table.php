<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('hospital_id');

            $table->string('name', 100);

            $table->enum('type', [
                'room',
                'bed',
                'clinic',
                'lab',
                'pharmacy',
            ]);

            $table->enum('status', [
                'available',
                'occupied',
                'maintenance',
                'reserved',
            ])->default('available');

            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};