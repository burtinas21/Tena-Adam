<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Paths to exclude from audit logging entirely.
     */
    private array $excludedPaths = [
        'api/audit-logs',
        'api/notifications',
        'api/up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (! auth()->check()) {
            return $response;
        }

        // Skip pure read requests (GET/HEAD) — only log mutations
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $response;
        }

        // Skip excluded paths
        foreach ($this->excludedPaths as $excluded) {
            if (str_starts_with($request->path(), $excluded)) {
                return $response;
            }
        }

        // Only log successful responses (2xx)
        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        [$action, $table] = $this->resolveAction($request);

        app(AuditLogService::class)->log(
            action:  $action,
            table:   $table,
            id:      null,
            details: [
                'method' => $request->method(),
                'path'   => $request->path(),
                'status' => $response->getStatusCode(),
            ],
        );

        return $response;
    }

    /**
     * Map HTTP method + path to a human-readable action + table name.
     */
    private function resolveAction(Request $request): array
    {
        $method = strtoupper($request->method());
        $path   = $request->path();  // e.g. "api/hospitals/uuid"

        // Strip the leading "api/" prefix for matching
        $segments = explode('/', ltrim(str_replace('api/', '', $path), '/'));
        $resource = $segments[0] ?? $path;

        // Convert kebab/snake resource to readable table name
        $table = str_replace('-', '_', $resource);

        $verbMap = [
            'POST'   => 'Created',
            'PUT'    => 'Updated',
            'PATCH'  => 'Updated',
            'DELETE' => 'Deleted',
        ];

        $resourceLabel = ucwords(str_replace(['-', '_'], ' ', $resource));
        $verb          = $verbMap[$method] ?? $method;

        // Special-case some known paths for clearer action names
        if (str_contains($path, '/approve'))   { return ["Approved {$resourceLabel}",    $table]; }
        if (str_contains($path, '/complete'))  { return ["Completed {$resourceLabel}",   $table]; }
        if (str_contains($path, '/cancel'))    { return ["Cancelled {$resourceLabel}",   $table]; }
        if (str_contains($path, '/reschedule')){ return ["Rescheduled {$resourceLabel}", $table]; }
        if (str_contains($path, 'login'))      { return ['Login',                         'auth']; }
        if (str_contains($path, 'logout'))     { return ['Logout',                        'auth']; }
        if (str_contains($path, 'register'))   { return ['Registered',                    'users']; }

        return ["{$verb} {$resourceLabel}", $table];
    }
}
