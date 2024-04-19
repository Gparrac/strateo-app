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
            'check-enterprise' => 2,
            'service-parameterization' => 59,
            'field-parameterization' => 60,
            'supplier-parameterization' => 58,
            'warehouse-parameterization' => 62,
            'measure-parameterization' => 63,
            'brand-parameterization' => 64,
            'category-parameterization' => 65,
            'inventory-parameterization' => 66,
            'product-parameterization' => 67,
            'employee-parameterization'=> 69,
            'invoice-parameterization' => 71,
            'tax-parameterization' => 72,
            'purchase-order-parameterization' => 73,
            'libretto-activity-parameterization' => 75,
            //Export
            'export-data/inventory-trades'=>66,
            'export-data/invoice-pdf'=>73,
            'tax-value-parameterization' => 72
        ];

        if (!array_key_exists($removeApiPrefix, $routesForm)) {
            return response()->json(['error' =>['server' => 'Path not found']], 404);
        }

        $formId = $routesForm[$removeApiPrefix];
        $request->merge(['formId' => $formId]);

        return $next($request);
    }
}
