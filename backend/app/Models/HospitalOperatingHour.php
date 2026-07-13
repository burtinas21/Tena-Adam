<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HospitalOperatingHour extends Model
{
    use HasFactory;


    protected $keyType = 'string';

    public $incrementing = false;


    protected $fillable = [

        'hospital_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_holiday',

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


    public function hospital()
    {

        return $this->belongsTo(
            Hospital::class
        );

    }
}