<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;


class TranslationKey extends Model
{


    use HasFactory;



    /*
    |--------------------------------------------------------------------------
    | UUID Configuration
    |--------------------------------------------------------------------------
    */

    protected $keyType = 'string';


    public $incrementing = false;




    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'key',

        'module',

        'description',

    ];




    /*
    |--------------------------------------------------------------------------
    | UUID Creation
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {


        parent::boot();



        static::creating(function ($translationKey) {


            if (!$translationKey->id) {


                $translationKey->id = Str::uuid();


            }


        });


    }





    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    |
    | TranslationKey
    |
    |      hasMany
    |
    | Translation
    |
    */

    public function translations(): HasMany
    {


        return $this->hasMany(
            Translation::class
        );


    }



}