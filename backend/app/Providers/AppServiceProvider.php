<?php

namespace App\Providers;
use App\Services\AuthService;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Queue;
use App\Policies\ConsultationPolicy;
use Illuminate\Support\Facades\Gate;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->bind(

            AuthServiceInterface::class,

            AuthService::class

        );

    }
public function boot(): void
{
   Gate::policy(
    Queue::class,
    ConsultationPolicy::class
);
     RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(1000)
            ->by($request->user()?->id ?: $request->ip());
    });
}
}
