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
        Schema::create('notifications', function (Blueprint $table) {

            $table->uuid('id')->primary();

             $table->foreignUuid('user_id')
              ->constrained('users')
              ->cascadeOnDelete();
            $table->enum('type', [
                'email',
                'sms',
                'push',
                'in_app',
            ]);

            $table->string('channel', 50);

            $table->string('subject')->nullable();

            $table->text('content');

            $table->timestamp('sent_at')->nullable();

            $table->enum('status', [
                'pending',
                'sent',
                'failed',
                'read',
            ])->default('pending');

            $table->text('error_message')->nullable();

            $table->unsignedInteger('retry_count')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};