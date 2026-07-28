<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ALTER COLUMN to extend the ENUM with the new value
        DB::statement("ALTER TABLE `medical_documents` MODIFY COLUMN `document_type` ENUM('lab_report','xray','mri','ct_scan','prescription','other','appointment_upload') NOT NULL");
    }

    public function down(): void
    {
        // Update any rows using the new value to 'other' before removing it
        DB::statement("UPDATE `medical_documents` SET `document_type` = 'other' WHERE `document_type` = 'appointment_upload'");
        DB::statement("ALTER TABLE `medical_documents` MODIFY COLUMN `document_type` ENUM('lab_report','xray','mri','ct_scan','prescription','other') NOT NULL");
    }
};
