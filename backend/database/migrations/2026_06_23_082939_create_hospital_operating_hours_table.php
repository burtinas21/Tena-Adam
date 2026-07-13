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
        Schema::create('hospital_operating_hours', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('hospital_id');

            $table->tinyInteger('day_of_week');

            $table->time('open_time');

            $table->time('close_time');

            $table->boolean('is_holiday')->default(false);

            $table->timestamps();

            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->cascadeOnDelete();

            $table->unique([
                'hospital_id',
                'day_of_week',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_operating_hours');
    }
};