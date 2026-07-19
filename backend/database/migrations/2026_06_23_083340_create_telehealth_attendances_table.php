<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telehealth_attendance', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('session_id')
                ->constrained('telehealth_sessions')
                ->cascadeOnDelete();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();

            $table->string('device_type', 50)->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telehealth_attendance');
    }
};
