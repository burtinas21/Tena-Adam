<?php

namespace App\Services;


use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class AuditLogService
{


    public function log(
        string $action,
        ?string $table=null,
        ?string $id=null,
        ?array $details=null
    )
    {


        AuditLog::create([

            'user_id'=>Auth::id(),

            'action'=>$action,

            'target_table'=>$table,

            'target_id'=>$id,

            'details'=>$details,

            'ip_address'=>request()->ip(),

            'user_agent'=>request()->userAgent(),

        ]);


    }



}