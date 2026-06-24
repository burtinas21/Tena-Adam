<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('first_name');
            $table->string('last_name');

            $table->string('email')->unique();

            $table->string('phone')->nullable();

            $table->string('password');

            $table->enum('role', [
                'patient',
                'doctor',
                'hospital_admin',
                'platform_admin'
            ])->nullable();

            $table->string('avatar_url')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamp('last_login')
                ->nullable();

            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};