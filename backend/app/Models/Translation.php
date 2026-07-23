<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Translation extends Model
{

    protected $keyType = 'string';

    public $incrementing = false;


    protected $fillable = [

        'translation_key_id',

        'language_id',

        'value'

    ];



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->id) {

                $model->id = Str::uuid();

            }

        });
    }



    /*
    |--------------------------------------------------------------------------
    | Language Relationship
    |--------------------------------------------------------------------------
    */

    public function language()
    {

        return $this->belongsTo(
            Language::class,
            'language_id'
        );

    }




    /*
    |--------------------------------------------------------------------------
    | Translation Key Relationship
    |--------------------------------------------------------------------------
    */

    public function translationKey()
    {

        return $this->belongsTo(
            TranslationKey::class,
            'translation_key_id'
        );

    }


}