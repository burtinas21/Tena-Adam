<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class MedicalEncounter extends Model
{
    use HasFactory;

    protected $table = 'medical_encounters';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [

        'id',

        'patient_id',

        'doctor_id',

        'hospital_id',

        'appointment_id',

        'encounter_date',

        'chief_complaint',

        'history',

        'physical_exam',

        'assessment',

        'diagnosis',

        'diagnosis_icd10',

        'treatment_plan',

        'clinical_notes',

        'follow_up_date',

        'status',

    ];

    protected $casts = [

        'encounter_date' => 'datetime',

        'follow_up_date' => 'date',

    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {

            if (empty($model->id)) {

                $model->id = (string) Str::uuid();

            }

        });
    }



    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id'
        );
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(
            HealthcareProvider::class,
            'doctor_id'
        );
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(
            Hospital::class,
            'hospital_id'
        );
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(
            Appointment::class,
            'appointment_id'
        );
    }
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'encounter_id');
    }
 public function vital(): HasOne
{
    return $this->hasOne(
        Vital::class,
        'encounter_id'
    );
}
public function medicalDocuments()
{
    return $this->hasMany(
        MedicalDocument::class,
        'encounter_id'
    );
}

}