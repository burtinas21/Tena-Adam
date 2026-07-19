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
        Schema::create('user_notification_preferences', function (Blueprint $table) {

           $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->boolean('email_enabled')
                ->default(true);

            $table->boolean('sms_enabled')
                ->default(true);

            $table->boolean('push_enabled')
                ->default(true);

            $table->boolean('appointment_reminders')
                ->default(true);

            $table->boolean('queue_updates')
                ->default(true);

            $table->boolean('promotional')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};