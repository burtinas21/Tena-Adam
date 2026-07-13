<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('healthcare_providers', function (Blueprint $table) {


            $table->date('practice_start_date')
                ->nullable()
                ->after('years_experience');


        });

    }



    public function down(): void
    {

        Schema::table('healthcare_providers', function (Blueprint $table) {


            $table->dropColumn(
                'practice_start_date'
            );


        });

    }

};