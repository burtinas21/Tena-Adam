<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Language;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'language_id',

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

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()
            ->whereIn('name', $roles)
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
public function telehealthAttendance()
{
    return $this->hasMany(TelehealthAttendance::class, 'user_id');
}
public function isAdmin(): bool
{
    return $this->hasAnyRole(['platform_admin', 'hospital_admin']);
}
public function notifications(): HasMany
{
    return $this->hasMany(
        Notification::class,
        'user_id'
    );
}
public function notificationPreference(): HasOne
{
    return $this->hasOne(
        UserNotificationPreference::class,
        'user_id'
    );
}
public function reports(): HasMany
{
    return $this->hasMany(
        Report::class,
        'created_by'
    );
}
public function uploadedMedicalDocuments()
{
    return $this->hasMany(
        MedicalDocument::class,
        'uploaded_by'
    );
}
public function auditLogs(): HasMany
{
    return $this->hasMany(
        AuditLog::class
    );
}
public function language(): BelongsTo
{

    return $this->belongsTo(
        Language::class,
        'language_id'
    );

}
public function approvedRefunds()
{
    return $this->hasMany(
        Refund::class,
        'approved_by'
    );
}
}
