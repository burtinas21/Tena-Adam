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
        Schema::create('users', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('first_name', 100);

            $table->string('last_name', 100);

            $table->string('email')->unique();

            $table->string('phone', 20)->nullable();

            $table->string('password');

            $table->string('avatar_url')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamp('last_login')->nullable();

            $table->rememberToken();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
