<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Patch existing hospital rows with real GPS coordinates.
     * Safe to run multiple times — only updates rows that still have NULL lat/lng.
     */
    public function up(): void
    {
        // Tikur Anbessa Specialized Hospital — Lideta, Addis Ababa
        DB::table('hospitals')
            ->where('code', 'TASH-001')
            ->whereNull('latitude')
            ->update([
                'latitude'        => 9.019287049008764,
                'longitude'       => 38.74875231172921,
                'google_place_id' => null,
            ]);

        // St. Paul's Hospital Millennium Medical College — Gulele, Addis Ababa
        DB::table('hospitals')
            ->where('code', 'SPHMMC-002')
            ->whereNull('latitude')
            ->update([
                'latitude'        => 9.048841379598851,
                'longitude'       => 38.729461441912484,
                'google_place_id' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('hospitals')
            ->whereIn('code', ['TASH-001', 'SPHMMC-002'])
            ->update([
                'latitude'        => null,
                'longitude'       => null,
                'google_place_id' => null,
            ]);
    }
};
