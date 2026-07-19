<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Vital extends Model
{
    use HasFactory;

    protected $table = 'vitals';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [

        'id',

        'encounter_id',

        'patient_id',

        'blood_pressure_systolic',

        'blood_pressure_diastolic',

        'pulse_rate',

        'respiratory_rate',

        'temperature',

        'weight',

        'height',

        'bmi',

        'blood_oxygen',

        'measured_at',

    ];

    protected $casts = [

        'temperature' => 'decimal:1',

        'weight' => 'decimal:2',

        'height' => 'decimal:2',

        'bmi' => 'decimal:2',

        'measured_at' => 'datetime',

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

    /**
     * Medical Encounter
     */
    public function encounter(): BelongsTo
    {
        return $this->belongsTo(
            MedicalEncounter::class,
            'encounter_id'
        );
    }

    /**
     * Patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id'
        );
    }
}