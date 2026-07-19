<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'type',
        'query',
        'parameters',
        'schedule',
        'last_run_at',
        'is_active',
        'created_by',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'last_run_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($report) {

            if (empty($report->id)) {
                $report->id = (string) Str::uuid();
            }

        });
    }

    /**
     * User who created the report.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
    public function hospital(): BelongsTo
{
    return $this->belongsTo(
        Hospital::class
    );
}
}