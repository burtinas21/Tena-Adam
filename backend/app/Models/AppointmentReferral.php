<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AppointmentReferral extends Model
{
    use HasFactory;

    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id',
        'appointment_id',
        'referred_by',
        'referred_to_doctor_id',
        'referred_to_department_id',
        'reason',
        'status',
        'rejection_reason',
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(HealthcareProvider::class, 'referred_by');
    }

    public function referredToDoctor(): BelongsTo
    {
        return $this->belongsTo(HealthcareProvider::class, 'referred_to_doctor_id');
    }

    public function referredToDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'referred_to_department_id');
    }
}
