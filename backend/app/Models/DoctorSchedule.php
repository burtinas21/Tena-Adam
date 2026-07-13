<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;
     public function getRouteKeyName()
    {
        return 'id';
    }

    protected $fillable = [

        'doctor_id',

        'day_of_week',

        'start_time',

        'end_time',

        'slot_duration_min',

        'lunch_start',

        'lunch_end',

        'is_available',

    ];

    protected $casts = [

        'is_available' => 'boolean',

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->id) {

                $model->id = Str::uuid();

            }

        });
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(

            HealthcareProvider::class,

            'doctor_id'

        );
    }
}