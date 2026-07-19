<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    use HasFactory;


    protected $keyType = 'string';

    public $incrementing = false;


    protected $fillable = [

        'name',
        'code',
        'address',
        'city',
        'region',
        'phone',
        'email',
        'website',
        'logo_url',
        'is_active',
        'registration_number',

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


    public function operatingHours()
    {

        return $this->hasMany(
            HospitalOperatingHour::class
        );

    }


    public function departments()
    {

        return $this->hasMany(
            Department::class
        );

    }


    public function staff()
    {

        return $this->hasMany(
            HospitalStaff::class
        );

    }


    public function facilities()
    {

        return $this->hasMany(
            Facility::class
        );

    }


    public function healthcareProviders()
    {

        return $this->hasMany(
            HealthcareProvider::class
        );

    }
    public function appointments()
{
    return $this->hasMany(
        Appointment::class,
        'hospital_id'
    );
}
public function queues()
{
    return $this->hasMany(Queue::class);
}
public function medicalEncounters()
{
    return $this->hasMany(
        MedicalEncounter::class,
        'hospital_id'
    );
}
public function reports(): HasMany
{
    return $this->hasMany(
        Report::class
    );
}
}