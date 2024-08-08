<?php

namespace App\Http\Middleware\Auth\RecoveryPassword;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SendEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
            ]);
            if ($validator->fails()){
                return response()->json(['error' => $validator->errors()], 400);
            }
            return $next($request);
        } catch (QueryException $ex) {
            Log::error('Query error Middleware@RecoveryPasswordSendEmail: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Credenciales invalidas.']], 400);
        } catch (\Exception $ex) {
            Log::error('unknown error Middleware@RecoveryPasswordSendEmail: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Error en el servidor']], 500);
        }

    }
}
