<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medication extends Model
{
    use HasFactory;

    public $incrementing = false;   // UUIDs are not auto-increment
    protected $keyType = 'string';  // UUID stored as string

    protected $fillable = [
        'name',
        'generic_name',
        'manufacturer',
        'dosage_form',
        'strength',
        'category',
        'requires_prescription',
        'side_effects',
        'interactions',
    ];
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'medication_id');
    }
}
