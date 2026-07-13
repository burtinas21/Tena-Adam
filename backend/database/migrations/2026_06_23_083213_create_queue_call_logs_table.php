<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_call_logs', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->uuid('queue_id');

            $table->uuid('called_by');

            $table->enum('call_method', [
                'app',
                'screen',
                'manual'
            ]);

            $table->timestamp('called_at')->useCurrent();

            $table->timestamps();

            $table->foreign('queue_id')
                ->references('id')
                ->on('queue')
                ->cascadeOnDelete();

            $table->foreign('called_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_call_logs');
    }
};