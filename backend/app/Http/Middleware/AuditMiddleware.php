<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use App\Services\AuditLogService;



class AuditMiddleware
{


    public function handle(
        Request $request,
        Closure $next
    )
    {


        $response=$next($request);



        if(auth()->check()){


            app(AuditLogService::class)
            ->log(

                action:$request->method()
                .' '.$request->path()

            );


        }


        return $response;


    }

}