<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QueueCallLog extends Model
{
    use HasFactory;

    protected $table = 'queue_call_logs';

    protected $fillable = [
        'queue_id',
        'called_by',
        'call_method',
        'called_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
    ];
        public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'called_by');
    }
}