<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class HealthcareProvider extends Model
{
    use HasUuids;

    protected $table = 'healthcare_providers';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'license_number',
        'department_id',
        'hospital_id',
        'consultation_fee',
        'years_experience',
        'bio',
        'profile_picture',
        'is_verified',
        'practice_start_date',
        'is_telehealth_available',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_telehealth_available' => 'boolean',
        'practice_start_date'=>'date',
    ];
protected $appends = [
    'profile_picture_url',
];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id',
            'id'
        );
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(
            Hospital::class
        );
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(
            Department::class
        );
    }
public function getYearsExperienceAttribute()
{

    if (!$this->practice_start_date) {

        return 0;

    }


    return (int) Carbon::parse(
        $this->practice_start_date
    )
    ->diffInYears(
        now()
    );

}
public function getProfilePictureUrlAttribute(): ?string
{
    if (!$this->profile_picture) {
        return null;
    }

    return asset(
        'storage/' . $this->profile_picture
    );
}
public function schedules(): HasMany
{
    return $this->hasMany(

        DoctorSchedule::class,

        'doctor_id'

    );
}
public function appointments()
{
    return $this->hasMany(
        Appointment::class,
        'doctor_id'
    );
}
public function leaves()
{
    return $this->hasMany(DoctorLeave::class, 'doctor_id');
}

public function slots()
{
    return $this->hasMany(AppointmentSlot::class, 'doctor_id');
}
public function queues()
{
    return $this->hasMany(Queue::class, 'doctor_id');
}
public function specializations(): BelongsToMany
{
    return $this->belongsToMany(
        Specialization::class,
        'doctor_specializations',
        'doctor_id',
        'specialization_id'
    )->withTimestamps();
}
public function medicalEncounters()
{
    return $this->hasMany(
        MedicalEncounter::class,
        'doctor_id'
    );
}
public function prescriptions()
    {
        return $this->hasManyThrough(
            Prescription::class,
            MedicalEncounter::class,
            'doctor_id',    // FK on encounters
            'encounter_id', // FK on prescriptions
            'id',           // Local key on doctors
            'id'            // Local key on encounters
        );
    }
    public function reviews()
{
    return $this->hasMany(
        ReviewRating::class,
        'doctor_id'
    );
}

public function telehealthSessions()
{
    return $this->hasManyThrough(
        TelehealthSession::class,
        Appointment::class,
        'doctor_id',      // FK on appointments
        'appointment_id', // FK on telehealth_sessions
        'id',             // Local key on healthcare_providers
        'id'              // Local key on appointments
    );
}

}
