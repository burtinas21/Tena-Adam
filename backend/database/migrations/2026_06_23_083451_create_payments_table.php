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
        Schema::create('payments', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->foreignUuid('appointment_id')
                ->nullable()
                ->constrained('appointments')
                ->nullOnDelete();
                 $table->foreignUuid('hospital_id')
                ->nullable()
                ->constrained('hospitals')
                ->nullOnDelete();

            $table->foreignUuid('patient_id')
                ->constrained('patients')
                ->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('ETB');
            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'refunded',
                'cancelled'
            ])->default('pending');
            $table->string('payment_method', 50);
            $table->string('transaction_id')->nullable();
            $table->string('reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('patient_id');
            $table->index('appointment_id');
            $table->index('status');
            $table->index('payment_method');
            $table->index('transaction_id');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};