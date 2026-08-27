<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Add hospital_id for tenant isolation.
            // Nullable because platform-level actions (login, register, etc.)
            // are not tied to any specific hospital.
            $table->foreignUuid('hospital_id')
                ->nullable()
                ->after('user_id')
                ->constrained('hospitals')
                ->nullOnDelete();

            $table->index('hospital_id');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropIndex(['hospital_id']);
            $table->dropColumn('hospital_id');
        });
    }
};
