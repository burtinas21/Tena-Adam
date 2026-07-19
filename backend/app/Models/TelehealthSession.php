<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TelehealthSession extends Model
{
    use HasFactory;

    protected $table = 'telehealth_sessions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'appointment_id',
        'session_url',
        'platform',
        'room_id',
        'meeting_id',
        'started_at',
        'ended_at',
        'duration_min',
        'recording_url',
        'recording_consent',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'recording_consent' => 'boolean',
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
        return $this->belongsTo(
            Appointment::class,
            'appointment_id'
        );
    }
    public function attendance()
{
    return $this->hasMany(TelehealthAttendance::class, 'session_id');
}

}