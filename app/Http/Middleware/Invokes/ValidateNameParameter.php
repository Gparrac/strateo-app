<?php

namespace App\Http\Middleware\Invokes;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateNameParameter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3', // Puedes ajustar las reglas de validación según tus necesidades
        ]);
        if ($validator->fails()){
            return response()->json([
                'error' => TRUE,
                'message' => $validator->errors()
            ], 400);
        }
        return $next($request);
    }
}
