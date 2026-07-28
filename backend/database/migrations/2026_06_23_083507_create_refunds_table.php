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
        Schema::create('refunds', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('payment_id')
                ->constrained('payments')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->text('reason')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'processed'
            ])->default('pending');

            $table->foreignUuid('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('refund_date')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('payment_id');
            $table->index('approved_by');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};