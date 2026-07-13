<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Facility extends Model
{
    use HasFactory;


    protected $keyType = 'string';

    public $incrementing = false;


    protected $fillable = [

        'hospital_id',
        'name',
        'type',
        'status',
        'description',

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