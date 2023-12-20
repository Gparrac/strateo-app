<?php

namespace App\Http\Middleware\CRUD\EnterpriseParameterization;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\CRUD\ValidateDataMiddlewareContext;

class EnterpriseParameterization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        switch($request->method()){
            case 'POST':
                $strategy = new ValidateDataMiddlewareContext(new CreateMiddleware());
                break;
            case 'GET':
                $strategy = new ValidateDataMiddlewareContext(new ReadMiddleware());
                break;
            case 'PUT':
                $strategy = new ValidateDataMiddlewareContext(new UpdateMiddleware());
                break;
            case 'DELETE':
                $strategy = new ValidateDataMiddlewareContext(new DeleteMiddleware());
                break;
            default:
                return response()->json(['error' => 'Method not allowed'], 400);
        }

        $execValidate = $strategy->execValidate($request);
        $statusResponse = $execValidate['statusResponse'] ?? 400;
        if($execValidate['error']) return response()->json(['error' => $execValidate['message']], $statusResponse);

        return $next($request);
    }
}
