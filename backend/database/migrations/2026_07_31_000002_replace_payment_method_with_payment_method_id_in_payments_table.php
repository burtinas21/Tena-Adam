<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the old plain-string column (and its index)
            if (Schema::hasColumn('payments', 'payment_method')) {
                $table->dropIndex(['payment_method']);
                $table->dropColumn('payment_method');
            }

            // Add the proper FK column
            if (! Schema::hasColumn('payments', 'payment_method_id')) {
                $table->uuid('payment_method_id')
                    ->nullable()
                    ->after('patient_id');

                $table->foreign('payment_method_id')
                    ->references('id')
                    ->on('payment_methods')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Restore the original string column
            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 50)->after('patient_id')->default('');
                $table->index('payment_method');
            }

            // Remove the FK column
            if (Schema::hasColumn('payments', 'payment_method_id')) {
                $table->dropForeign(['payment_method_id']);
                $table->dropColumn('payment_method_id');
            }
        });
    }
};
