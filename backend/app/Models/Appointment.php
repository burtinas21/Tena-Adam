<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\TelehealthSession;

class Appointment extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'department_id',
        'slot_id',
        'scheduled_time',
        'duration_min',
        'status',
        'reason',
        'notes',
        'cancel_reason',
        'cancelled_at',
        'approved_at',
        'approved_by',
        'is_telehealth',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'approved_at'    => 'datetime',
        'cancelled_at'   => 'datetime',
        'is_telehealth'  => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {

            if (!$appointment->id) {

                $appointment->id = Str::uuid();

            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
    public function patient()
{
    return $this->belongsTo(
        Patient::class,
        'patient_id'
    );
}

public function doctor()
{
    return $this->belongsTo(
        HealthcareProvider::class,
        'doctor_id'
    );
}

public function hospital()
{
    return $this->belongsTo(
        Hospital::class,
        'hospital_id'
    );
}

public function department()
{
    return $this->belongsTo(
        Department::class,
        'department_id'
    );
}

public function approvedBy()
{
    return $this->belongsTo(
        User::class,
        'approved_by'
    );
}
public function slot()
{
    return $this->belongsTo(
        AppointmentSlot::class,
        'slot_id'
    );
}
public function queue()
{
    return $this->hasOne(Queue::class);
}
public function medicalEncounter()
{
    return $this->hasOne(
        MedicalEncounter::class,
        'appointment_id'
    );
}




/**
 * Telehealth session.
 */
public function telehealthSession(): HasOne
{
    return $this->hasOne(
        TelehealthSession::class,
        'appointment_id'
    );
}
public function review()
{
    return $this->hasOne(
        ReviewRating::class
    );
}

public function referrals()
{
    return $this->hasMany(
        AppointmentReferral::class,
        'appointment_id'
    );
}

/** Documents uploaded at booking time (before an encounter exists). */
public function documents()
{
    return $this->hasMany(
        MedicalDocument::class,
        'appointment_id'
    );
}
public function payment()
{
    return $this->hasOne(Payment::class);
}
}