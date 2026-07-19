<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->uuid('id')->primary();


            /*
            |--------------------------------------------------------------------------
            | User who performed the action
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Action information
            |--------------------------------------------------------------------------
            */

            $table->string('action',50);


            /*
            |--------------------------------------------------------------------------
            | Target information
            |--------------------------------------------------------------------------
            */

            $table->string('target_table',50)
                ->nullable();


            $table->uuid('target_id')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Extra details
            |--------------------------------------------------------------------------
            */

            $table->json('details')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Security information
            |--------------------------------------------------------------------------
            */

            $table->string('ip_address',45)
                ->nullable();


            $table->string('user_agent',255)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Created only
            |--------------------------------------------------------------------------
            */

            $table->timestamp('created_at')
                ->useCurrent();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};