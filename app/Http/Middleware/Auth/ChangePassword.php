<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ChangePassword
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
                'old_password' => 'required|string',
                'new_password' => 'required|string',
            ]);

            if ($validator->fails()){
                return response()->json(['error' => $validator->errors()], 400);
            }
            $user = Auth::user();
            if (!$user || !password_verify($request->input('old_password'), $user->password)) {
                return response()->json(['error' => ['auth' => 'Invalid Credentials.']], 400);
            }
            return $next($request);
        } catch (QueryException $ex) {
            Log::error('Query error Middleware@DataChangePassword: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Invalid Credentials.']], 400);
        } catch (\Exception $ex) {
            Log::error('unknown error Middleware@DataChangePassword: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Error en el servidor']], 500);
        }

    }
}
