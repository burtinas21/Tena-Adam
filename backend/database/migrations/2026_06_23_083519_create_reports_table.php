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
        Schema::create('reports', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->uuid('hospital_id');
            $table->string('name', 100);
            $table->enum('type', [
                'appointment',
                'patient',
                'doctor',
                'revenue',
                'telehealth',
                'custom',
            ]);

            $table->text('query');

            $table->json('parameters')->nullable();

            $table->string('schedule', 50)->nullable();

            $table->dateTime('last_run_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->uuid('created_by');

            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
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
        Schema::dropIfExists('reports');
    }
};