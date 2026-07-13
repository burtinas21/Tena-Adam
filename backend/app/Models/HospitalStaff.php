<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HospitalStaff extends Model
{
    use HasFactory;


    protected $keyType = 'string';

    public $incrementing = false;



    protected $fillable = [

        'user_id',
        'hospital_id',
        'position',
        'department_id',
        'hire_date',
        'is_active',

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



    public function user()
    {

        return $this->belongsTo(
            User::class
        );

    }



    public function hospital()
    {

        return $this->belongsTo(
            Hospital::class
        );

    }



    public function department()
    {

        return $this->belongsTo(
            Department::class
        );

    }
}