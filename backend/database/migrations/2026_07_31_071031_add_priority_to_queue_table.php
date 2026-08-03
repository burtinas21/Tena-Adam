<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->unsignedTinyInteger('priority')->default(0)->after('queue_number');
        });
    }

    public function down(): void
    {
        Schema::table('queue', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
