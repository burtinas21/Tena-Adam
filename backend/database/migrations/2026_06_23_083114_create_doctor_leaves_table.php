<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_leaves', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('doctor_id');

            $table->date('leave_date');

            $table->string('reason')->nullable();

            $table->enum('leave_type', [
                'vacation',
                'sick',
                'training',
                'other'
            ]);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->uuid('approved_by')->nullable();
            $table->timestamps();
            $table->unique(['doctor_id', 'leave_date']);
            $table->foreign('doctor_id')
                ->references('id')
                ->on('healthcare_providers')
                ->cascadeOnDelete();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_leaves');
    }
};