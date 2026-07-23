<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Language extends Model
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
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'code',

        'name',

        'native_name',

        'direction',

        'is_active',

        'is_default',

    ];



    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'is_active'  => 'boolean',

        'is_default' => 'boolean',

    ];



    /*
    |--------------------------------------------------------------------------
    | UUID Generate
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {

        parent::boot();


        static::creating(function ($language) {


            if (!$language->id) {

                $language->id = Str::uuid();

            }


        });


    }



    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Language
    |    |
    |    | hasMany
    |    |
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