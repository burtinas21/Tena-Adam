<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SymptomDepartmentMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'symptom_id',
        'department_id',
        'relevance_score',
        'is_primary',
        'evidence_level',
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
        return $this->belongsTo(Department::class);
    }
}
