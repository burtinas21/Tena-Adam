<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use CanResetPassword,
        HasApiTokens,
        HasFactory,
        Notifiable;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [

        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar_url',
        'is_active',
        'last_login',

    ];

    protected $hidden = [

        'password',
        'remember_token',

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

            'user_roles'

        )
            ->withPivot('assigned_by')
            ->withTimestamps();

    }

    public function patient()
    {

        return $this->hasOne(

            Patient::class,

            'id'

        );

    }

    public function hasRole($role)
    {

        return $this->roles()

            ->where('name', $role)

            ->exists();

    }

    public function hasPermission($permission)
    {

        return $this->roles()
            ->whereHas(

                'permissions',

                function ($query) use ($permission) {

                    $query->where(
                        'name',
                        $permission
                    );

                }

            )
            ->exists();

    }

    public function sendPasswordResetNotification($token)
    {

        $url = config('app.frontend_url')
            .'/reset-password?token='
            .$token
            .'&email='
            .$this->email;

        $this->notify(

            new ResetPasswordNotification($url)

        );

    }
    public function hospitalStaff()
{

    return $this->hasMany(
        HospitalStaff::class
    );

}


public function hospitals()
{

    return $this->belongsToMany(

        Hospital::class,

        'hospital_staff'

    )
    ->withPivot([
        'position',
        'department_id',
        'is_active'
    ])
    ->withTimestamps();

}
public function healthcareProvider()
{
    return $this->hasOne(
        HealthcareProvider::class, 'id','id'
    );
}
public function appointments()
{
    return $this->hasMany(
        Appointment::class,
        'patient_id'
    );
}

public function approvedAppointments()
{
    return $this->hasMany(
        Appointment::class,
        'approved_by'
    );
}
public function approvedLeaves(): HasMany
{
    return $this->hasMany(

        DoctorLeave::class,

        'approved_by'

    );
}
}
