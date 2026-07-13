<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DoctorLeave extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'doctor_id',

        'leave_date',

        'reason',

        'leave_type',

        'status',

        'approved_by',

    ];

    protected $casts = [

        'leave_date' => 'date',

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (! $model->id) {

                $model->id = Str::uuid();

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

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(

            User::class,

            'approved_by'

        );
    }
}