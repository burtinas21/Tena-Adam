<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add all missing columns to medical_documents.
     * The original migration ran before medical_encounters existed,
     * so the foreign key constraint caused silent failure leaving only
     * id, created_at, updated_at.
     */
    public function up(): void
    {
        Schema::table('medical_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_documents', 'patient_id')) {
                $table->foreignUuid('patient_id')
                    ->after('id')
                    ->constrained('patients')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('medical_documents', 'encounter_id')) {
                $table->foreignUuid('encounter_id')
                    ->nullable()
                    ->after('patient_id')
                    ->constrained('medical_encounters')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('medical_documents', 'file_name')) {
                $table->string('file_name', 255)->after('encounter_id');
            }

            if (!Schema::hasColumn('medical_documents', 'file_url')) {
                $table->string('file_url', 500)->after('file_name');
            }

            if (!Schema::hasColumn('medical_documents', 'file_type')) {
                $table->string('file_type', 100)->nullable()->after('file_url');
            }

            if (!Schema::hasColumn('medical_documents', 'file_size')) {
                $table->integer('file_size')->nullable()->after('file_type');
            }

            if (!Schema::hasColumn('medical_documents', 'document_type')) {
                $table->enum('document_type', [
                    'lab_report',
                    'xray',
                    'mri',
                    'ct_scan',
                    'prescription',
                    'other',
                ])->after('file_size');
            }

            if (!Schema::hasColumn('medical_documents', 'uploaded_by')) {
                $table->foreignUuid('uploaded_by')
                    ->after('document_type')
                    ->constrained('users')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('medical_documents', 'description')) {
                $table->text('description')->nullable()->after('uploaded_by');
            }
        });
    }

    /**
     * Drop all the columns added above (except id and timestamps).
     */
    public function down(): void
    {
        Schema::table('medical_documents', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['encounter_id']);
            $table->dropForeign(['uploaded_by']);

            $table->dropColumn([
                'patient_id',
                'encounter_id',
                'file_name',
                'file_url',
                'file_type',
                'file_size',
                'document_type',
                'uploaded_by',
                'description',
            ]);
        });
    }
};
