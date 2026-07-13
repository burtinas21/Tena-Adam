<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescription extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'encounter_id',
        'medication_id',
        'medication_name',
        'dosage',
        'frequency',
        'route',
        'duration_days',
        'quantity',
        'instructions',
        'refills',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Prescription belongs to one medical encounter
    public function encounter()
    {
        return $this->belongsTo(MedicalEncounter::class, 'encounter_id');
    }

    // Prescription may reference a catalog medication
    public function medication()
    {
        return $this->belongsTo(Medication::class, 'medication_id');
    }

    // Prescription belongs to a patient (via encounter)
    public function patient()
    {
        return $this->hasOneThrough(
            Patient::class,
            MedicalEncounter::class,
            'id',          // FK on encounters
            'id',          // FK on patients
            'encounter_id',// Local key
            'patient_id'   // Encounter → Patient
        );
    }

    // Prescription belongs to a doctor (via encounter)
    public function doctor()
    {
        return $this->hasOneThrough(
            HealthcareProvider::class,
            MedicalEncounter::class,
            'id',
            'id',
            'encounter_id',
            'doctor_id'
        );
    }
}
