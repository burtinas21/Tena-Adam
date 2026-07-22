<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->uuid('id')
                ->primary();


            /*
            |--------------------------------------------------------------------------
            | Language Information
            |--------------------------------------------------------------------------
            */

            // Example:
            // en
            // am
            // om
            // ti

            $table->string('code',10)
                ->unique();


            // English
            // Amharic
            // Afaan Oromo

            $table->string('name',100);



            /*
            |--------------------------------------------------------------------------
            | Native Language Name
            |--------------------------------------------------------------------------
            |
            | Example:
            | English
            | አማርኛ
            | Afaan Oromoo
            |
            */

            $table->string('native_name',100);



            /*
            |--------------------------------------------------------------------------
            | Direction
            |--------------------------------------------------------------------------
            |
            | Most languages are LTR.
            | Future support Arabic etc.
            |
            */

            $table->enum(
                'direction',
                [
                    'ltr',
                    'rtl'
                ]
            )
            ->default('ltr');



            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);



            /*
            |--------------------------------------------------------------------------
            | Default Language
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_default')
                ->default(false);



            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('is_active');

        });

    }



    public function down(): void
    {
        Schema::dropIfExists('languages');
    }

};