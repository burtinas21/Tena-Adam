<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'name',
        'description',
        'module',

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

    public function roles()
    {

        return $this->belongsToMany(

            Role::class,

            'role_permissions'

        )
            ->withTimestamps();

    }
}
