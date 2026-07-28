<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalDocument extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'encounter_id',
        'file_name',
        'file_url',
        'file_type',
        'file_size',
        'document_type',
        'uploaded_by',
        'description',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class
        );
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(
            MedicalEncounter::class,
            'encounter_id'
        );
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(
            Appointment::class,
            'appointment_id'
        );
    }
}