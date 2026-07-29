<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;


    protected $keyType = 'string';

    public $incrementing = false;


    protected $fillable = [

        'hospital_id',
        'name',
        'description',
        'head_doctor_id',
        'parent_department_id',
        'is_active',

    ];

    /**
     * Decode the name if stored as a JSON translation object {"en":"..."}.
     */
    public function getNameAttribute($value): string
    {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded['en'] ?? $decoded[array_key_first($decoded)] ?? $value;
        }
        return $value ?? '';
    }


    protected static function boot()
    {

        parent::boot();


        static::creating(function ($model) {

            if (! $model->id) {

                $model->id = Str::uuid();

            }

        });

    }



    public function hospital()
    {

        return $this->belongsTo(
            Hospital::class
        );

    }



    public function parent()
    {

        return $this->belongsTo(
            Department::class,
            'parent_department_id'
        );

    }



    public function children()
    {

        return $this->hasMany(
            Department::class,
            'parent_department_id'
        );

    }



    public function headDoctor()
    {

        return $this->belongsTo(
            HealthcareProvider::class,
            'head_doctor_id'
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
        'department_id'
    );
}
public function symptomMappings()
    {
        return $this->hasMany(SymptomDepartmentMapping::class);
    }

    public function analytics()
    {
        return $this->hasMany(SymptomAnalytic::class, 'recommended_department_id');
    }
}