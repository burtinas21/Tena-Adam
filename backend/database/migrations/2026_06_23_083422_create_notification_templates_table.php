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
        Schema::create('notification_templates', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('name', 100)
                ->unique();

            $table->string('subject')
                ->nullable();

            $table->text('email_body')
                ->nullable();

            $table->text('sms_body')
                ->nullable();

            $table->text('push_body')
                ->nullable();

            $table->json('variables')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};