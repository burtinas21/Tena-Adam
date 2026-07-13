<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('hospital_id');

            $table->string('name', 100);

            $table->text('description')->nullable();

            $table->uuid('head_doctor_id')->nullable();

            $table->uuid('parent_department_id')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();


            $table->foreign('hospital_id')
                ->references('id')
                ->on('hospitals')
                ->cascadeOnDelete();


            $table->foreign('parent_department_id')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();


            $table->unique([
                'hospital_id',
                'name',
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};