<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Medication extends Model
{
    use HasFactory;

    protected $table = 'medications';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'generic_name',
        'manufacturer',
        'dosage_form',
        'strength',
        'category',
        'requires_prescription',
        'side_effects',
        'interactions',
    ];

    protected $casts = [
        'requires_prescription' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {

            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

        });
    }

    /**
     * Medication can appear in many prescriptions.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(
            Prescription::class,
            'medication_id'
        );
    }
}