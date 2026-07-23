<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {

        Schema::create('translation_keys', function (Blueprint $table) {


            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->uuid('id')
                ->primary();



            /*
            |--------------------------------------------------------------------------
            | Translation Key
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | dashboard.title
            | patient.register
            |
            */

            $table->string(
                'key',
                255
            )
            ->unique();



            /*
            |--------------------------------------------------------------------------
            | Module
            |--------------------------------------------------------------------------
            |
            | Helps organize translations
            |
            | dashboard
            | patient
            | appointment
            | emr
            |
            */

            $table->string(
                'module',
                100
            )
            ->nullable();



            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

           $table->text('description')
    ->nullable();



            $table->timestamps();



            $table->index('module');


        });

    }



    public function down(): void
    {
        Schema::dropIfExists('translation_keys');
    }


};