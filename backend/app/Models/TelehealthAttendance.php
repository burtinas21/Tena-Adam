<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelehealthAttendance extends Model
{
    protected $table = 'telehealth_attendance';

    protected $fillable = [
        'session_id',
        'user_id',
        'joined_at',
        'left_at',
        'device_type',
        'ip_address',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TelehealthSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
