<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescriptions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {

            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

        });
    }
    public function encounter(): BelongsTo
    {
        return $this->belongsTo(
            MedicalEncounter::class,
            'encounter_id'
        );
    }
    public function medication(): BelongsTo
    {
        return $this->belongsTo(
            Medication::class,
            'medication_id'
        );
    }
}