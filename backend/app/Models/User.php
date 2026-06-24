<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Str;


class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens;



    // UUID configuration
    public $incrementing = false;

    protected $keyType = 'string';



    protected $fillable = [

        'first_name',

        'last_name',

        'email',

        'password',

        'phone',

        'role'

    ];



    protected $hidden = [

        'password',

        'remember_token'

    ];



    protected static function boot()
    {

        parent::boot();



        static::creating(function ($user) {


            if (!$user->id) {

                $user->id = (string) Str::uuid();

            }


        });


    }



    public function sendPasswordResetNotification($token)
    {

        $this->notify(
            new ResetPassword($token)
        );

    }



    protected function casts(): array
    {

        return [

            'email_verified_at'=>'datetime',

            'password'=>'hashed',

        ];

    }



    public function roles()
    {

        return $this->belongsToMany(

            Role::class,

            'user_roles'

        );

    }



    public function hasRole($role)
    {

        return $this->roles()

            ->where('name',$role)

            ->exists();

    }



    public function hasPermission($permission)
    {

        return $this->roles()

            ->whereHas('permissions', function($q) use($permission){

                $q->where('name',$permission);

            })

            ->exists();

    }


}