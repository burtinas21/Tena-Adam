<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Symptom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = Str::uuid();
            }
        });
    }

    // 
    public function departmentMappings()
    {
        return $this->hasMany(SymptomDepartmentMapping::class);
    }

    public function analytics()
    {
        return $this->hasMany(SymptomAnalytic::class, 'symptom_id');
    }
}
