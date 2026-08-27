<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AuditLog extends Model
{

    protected $table = 'audit_logs';


    public $incrementing = false;


    protected $keyType = 'string';



    /*
    |--------------------------------------------------------------------------
    | Disable updated_at
    |--------------------------------------------------------------------------
    */

    public $timestamps = false;



    protected $fillable = [

        'user_id',

        'hospital_id',

        'action',

        'target_table',

        'target_id',

        'details',

        'ip_address',

        'user_agent',

    ];



    protected $casts = [

        'details'=>'array',

        'created_at'=>'datetime',

    ];



    protected static function boot()
    {
        parent::boot();


        static::creating(function($audit){

            if(!$audit->id){

                $audit->id = Str::uuid();

            }

        });

    }
    public function user(): BelongsTo
    {

        return $this->belongsTo(
            User::class
        );

    }

    public function hospital(): BelongsTo
    {

        return $this->belongsTo(
            Hospital::class
        );

    }


}