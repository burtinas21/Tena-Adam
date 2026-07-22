<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Language extends Model
{
    use HasFactory;

    protected $table = 'languages';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($language) {

            if (!$language->id) {
                $language->id = (string) Str::uuid();
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function translations()
    {
        return $this->hasMany(
            Translation::class
        );
    }

    public function users()
    {
        return $this->hasMany(
            User::class,
            'preferred_language_id'
        );
    }

    public function hospitalTranslations()
    {
        return $this->hasMany(
            HospitalTranslation::class
        );
    }

    public function departmentTranslations()
    {
        return $this->hasMany(
            DepartmentTranslation::class
        );
    }

    public function medicineTranslations()
    {
        return $this->hasMany(
            MedicineTranslation::class
        );
    }

    public function symptomTranslations()
    {
        return $this->hasMany(
            SymptomTranslation::class
        );
    }

    public function diseaseTranslations()
    {
        return $this->hasMany(
            DiseaseTranslation::class
        );
    }

    public function medicalServiceTranslations()
    {
        return $this->hasMany(
            MedicalServiceTranslation::class
        );
    }
}