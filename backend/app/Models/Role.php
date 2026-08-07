<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'name',
        'description',
        'is_default',
        'hospital_id',

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

    public function permissions()
    {

        return $this->belongsToMany(

            Permission::class,

            'role_permissions'

        )
            ->withTimestamps();

    }

    public function users()
    {

        return $this->belongsToMany(

            User::class,

            'user_roles'

        )
            ->withPivot('assigned_by')
            ->withTimestamps();

    }
}
