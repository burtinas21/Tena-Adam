<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Translation extends Model
{
    use HasFactory;

    protected $table = 'translations';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'translation_key_id',
        'language_id',
        'value',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }

        });
    }

    public function translationKey()
    {
        return $this->belongsTo(
            TranslationKey::class
        );
    }

    public function language()
    {
        return $this->belongsTo(
            Language::class
        );
    }
}