<?php

namespace App\Http\Middleware\Auth\Role;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RouteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();
        $removeApiPrefix = str_replace('api/', '', $path);

        $routesForm = [
            'enterprise-parameterization' => 2,
            'client-parameterization' => 2,
            'role-parameterization' => 3,
            'user-parameterization' => 5,
            'office-parameterization' => 6,
            'check-enterprise' => 2
        ];

        if (!array_key_exists($removeApiPrefix, $routesForm)) {
            return response()->json(['error' =>['server' => 'Path not found']], 404);
        }

        $formId = $routesForm[$removeApiPrefix];
        $request->merge(['formId' => $formId]);

        return $next($request);
    }
}
