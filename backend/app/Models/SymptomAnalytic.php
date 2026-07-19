<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SymptomAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'symptom_id',
        'recommended_department_id',
        'selected_by_patient',
        'patient_id',
        'session_id',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function symptom()
    {
        return $this->belongsTo(Symptom::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'recommended_department_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
