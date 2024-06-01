<?php

namespace App\Http\Middleware\Metrics;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\CRUD\ValidateDataMiddlewareContext;

class Analytic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,): Response
    {
        $type = $request->route('type');
        switch($type){
            case 'client':
                $strategy = new ValidateDataMiddlewareContext(new CustomerMiddleware());
                break;
            case 'seller':
                    $strategy = new ValidateDataMiddlewareContext(new CustomerMiddleware());
                    break;
            case 'invoice':
                $strategy = new ValidateDataMiddlewareContext(new CustomerMiddleware());
                        break;
            default:
                return response()->json(['error' => 'Method not allowed'], 400);
        }

        $execValidate = $strategy->execValidate($request);
        if($execValidate['error']) return response()->json(['error' => $execValidate['message']], 400);

        return $next($request);
    }
}
