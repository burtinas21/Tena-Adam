<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewRating extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [

        'patient_id',

        'doctor_id',

        'appointment_id',

        'rating',

        'comment',

        'is_anonymous',

    ];


    protected $casts = [

        'is_anonymous' => 'boolean',

        'rating' => 'integer',

    ];


    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class
        );
    }


    public function doctor(): BelongsTo
    {
        return $this->belongsTo(
            HealthcareProvider::class,
            'doctor_id'
        );
    }


    public function appointment(): BelongsTo
    {
        return $this->belongsTo(
            Appointment::class
        );
    }
}