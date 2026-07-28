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
        Schema::create('invoices', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete();

            $table->string('invoice_number', 50)->unique();

            $table->date('due_date');

            $table->enum('status', [
                'paid',
                'unpaid',
                'overdue',
                'cancelled'
            ])->default('unpaid');

            $table->string('pdf_url')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('payment_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};