<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AppointmentSlot extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'doctor_id',

        'appointment_id',

        'start_time',

        'end_time',

        'status',

    ];

    protected $casts = [

        'start_time' => 'datetime',

        'end_time'   => 'datetime',

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($slot) {

            if (!$slot->id) {

                $slot->id = Str::uuid();

            }

        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(
            HealthcareProvider::class,
            'doctor_id'
        );
    }

   public function appointment()
{
    return $this->hasOne(
        Appointment::class,
        'slot_id'
    );
}
}