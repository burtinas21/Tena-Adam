<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Patient extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'id',

        'date_of_birth',

        'gender',

        'address',

        // 'emergency_contact',

        'blood_type',

        'allergies',

        'medical_history',

        'national_id',

        'occupation',

        'patient_status',

        'registered_by',

    ];

    public function user()
    {

        return $this->belongsTo(

            User::class,

            'id'

        );

    }

    public function registeredBy()
    {

        return $this->belongsTo(

            User::class,

            'registered_by'

        );

    }
    public function emergencyContacts(): HasMany
{
    return $this->hasMany(

        PatientEmergencyContact::class,

        'patient_id'

    );
}
public function medicalEncounters()
{
    return $this->hasMany(
        MedicalEncounter::class,
        'patient_id'
    );
}
public function prescriptions()
    {
        return $this->hasManyThrough(
            Prescription::class,
            MedicalEncounter::class,
            'patient_id',   // FK on encounters
            'encounter_id', // FK on prescriptions
            'id',           // Local key on patients
            'id'            // Local key on encounters
        );
    }
    public function vitals()
{
    return $this->hasMany(Vital::class, 'patient_id');
}

}
