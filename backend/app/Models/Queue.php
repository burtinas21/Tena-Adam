<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Queue extends Model
{
    use HasFactory;

    protected $table = 'queue';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'appointment_id',
        'doctor_id',
        'hospital_id',
        'queue_date',
        'queue_number',
        'status',
        'called_at',
        'started_at',
        'ended_at',
        'walk_in_patient_name',
        'walk_in_phone',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'called_at'  => 'datetime',
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
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

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(HealthcareProvider::class, 'doctor_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function callLogs()
    {
        return $this->hasMany(QueueCallLog::class);
    }
}