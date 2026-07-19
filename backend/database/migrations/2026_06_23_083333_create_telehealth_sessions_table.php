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
        Schema::create('telehealth_sessions', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('appointment_id')
                ->unique()
                ->constrained('appointments')
                ->cascadeOnDelete();

            $table->string('session_url', 500);

            $table->enum('platform', [
                'google_meet',
                'zoom',
                'microsoft_teams',
                'custom',
            ]);

            $table->string('room_id', 100)->nullable();

            $table->string('meeting_id', 100)->nullable();

            $table->timestamp('started_at')->nullable();

            $table->timestamp('ended_at')->nullable();

            $table->integer('duration_min')->nullable();

            $table->string('recording_url', 500)->nullable();

            $table->boolean('recording_consent')->default(false);

            $table->enum('status', [
                'scheduled',
                'active',
                'completed',
                'cancelled',
            ])->default('scheduled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telehealth_sessions');
    }
};