<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PatientEmergencyContact extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'patient_id',

        'name',

        'relationship',

        'phone',

        'email',

        'address',

        'is_primary',

    ];

    protected $casts = [

        'is_primary' => 'boolean',

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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(

            Patient::class,

            'patient_id'

        );
    }
}