<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{


    public function up(): void
    {


        Schema::create('translations', function (Blueprint $table) {



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
            */

            $table->foreignUuid(
                'translation_key_id'
            )
            ->constrained(
                'translation_keys'
            )
            ->cascadeOnDelete();




            /*
            |--------------------------------------------------------------------------
            | Language
            |--------------------------------------------------------------------------
            */

            $table->foreignUuid(
                'language_id'
            )
            ->constrained(
                'languages'
            )
            ->cascadeOnDelete();





            /*
            |--------------------------------------------------------------------------
            | Translation Text
            |--------------------------------------------------------------------------
            */

            $table->longText(
                'value'
            );




            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->uuid('created_by')
                ->nullable();



            $table->uuid('updated_by')
                ->nullable();




            $table->timestamps();




            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Translation
            |--------------------------------------------------------------------------
            |
            | Same key cannot have duplicate language
            |
            */

            $table->unique(
                [
                    'translation_key_id',
                    'language_id'
                ]
            );



            $table->index(
                'language_id'
            );


        });


    }



    public function down(): void
    {

        Schema::dropIfExists('translations');

    }


};