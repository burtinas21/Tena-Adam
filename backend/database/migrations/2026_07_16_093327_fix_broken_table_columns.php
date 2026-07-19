<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Several migrations ran before their referenced tables existed (FK constraint
 * failure), leaving tables with only id + timestamps.  This migration adds the
 * missing columns to those tables safely using hasColumn() guards.
 *
 * Affected tables: review_ratings, audit_logs, reports
 */
return new class extends Migration
{
    public function up(): void
    {
        /*
        |----------------------------------------------------------------------
        | review_ratings
        |----------------------------------------------------------------------
        | Needs: patient_id, doctor_id, appointment_id, rating, comment,
        |        is_anonymous
        | FK to appointments/healthcare_providers/patients — all exist now.
        */
        Schema::table('review_ratings', function (Blueprint $table) {
            if (!Schema::hasColumn('review_ratings', 'patient_id')) {
                $table->uuid('patient_id')->after('id');
                $table->foreign('patient_id')
                    ->references('id')->on('patients')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('review_ratings', 'doctor_id')) {
                $table->uuid('doctor_id')->after('patient_id');
                $table->foreign('doctor_id')
                    ->references('id')->on('healthcare_providers')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('review_ratings', 'appointment_id')) {
                $table->uuid('appointment_id')->unique()->after('doctor_id');
                $table->foreign('appointment_id')
                    ->references('id')->on('appointments')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('review_ratings', 'rating')) {
                $table->unsignedTinyInteger('rating')->after('appointment_id');
            }
            if (!Schema::hasColumn('review_ratings', 'comment')) {
                $table->text('comment')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('review_ratings', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false)->after('comment');
            }
        });

        /*
        |----------------------------------------------------------------------
        | audit_logs
        |----------------------------------------------------------------------
        | The original migration was a stub (id + timestamps only).
        | Adding proper columns for audit trail.
        */
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'user_id')) {
                $table->uuid('user_id')->nullable()->after('id');
                $table->foreign('user_id')
                    ->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action', 100)->after('user_id');
            }
            if (!Schema::hasColumn('audit_logs', 'auditable_type')) {
                $table->string('auditable_type', 100)->nullable()->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'auditable_id')) {
                $table->uuid('auditable_id')->nullable()->after('auditable_type');
            }
            if (!Schema::hasColumn('audit_logs', 'old_values')) {
                $table->json('old_values')->nullable()->after('auditable_id');
            }
            if (!Schema::hasColumn('audit_logs', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('new_values');
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });

        /*
        |----------------------------------------------------------------------
        | reports
        |----------------------------------------------------------------------
        | Needs: hospital_id, name, type, query, parameters, schedule,
        |        last_run_at, is_active, created_by
        */
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'hospital_id')) {
                $table->uuid('hospital_id')->nullable()->after('id');
                $table->foreign('hospital_id')
                    ->references('id')->on('hospitals')->restrictOnDelete();
            }
            if (!Schema::hasColumn('reports', 'name')) {
                $table->string('name', 100)->after('hospital_id');
            }
            if (!Schema::hasColumn('reports', 'type')) {
                $table->enum('type', [
                    'appointment', 'patient', 'doctor',
                    'revenue', 'telehealth', 'custom',
                ])->after('name');
            }
            if (!Schema::hasColumn('reports', 'query')) {
                $table->text('query')->nullable()->after('type');
            }
            if (!Schema::hasColumn('reports', 'parameters')) {
                $table->json('parameters')->nullable()->after('query');
            }
            if (!Schema::hasColumn('reports', 'schedule')) {
                $table->string('schedule', 50)->nullable()->after('parameters');
            }
            if (!Schema::hasColumn('reports', 'last_run_at')) {
                $table->dateTime('last_run_at')->nullable()->after('schedule');
            }
            if (!Schema::hasColumn('reports', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('last_run_at');
            }
            if (!Schema::hasColumn('reports', 'created_by')) {
                $table->uuid('created_by')->nullable()->after('is_active');
                $table->foreign('created_by')
                    ->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('review_ratings', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['appointment_id']);
            $table->dropColumn(['patient_id', 'doctor_id', 'appointment_id', 'rating', 'comment', 'is_anonymous']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'action', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'ip_address', 'user_agent']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['hospital_id', 'name', 'type', 'query', 'parameters', 'schedule', 'last_run_at', 'is_active', 'created_by']);
        });
    }
};
