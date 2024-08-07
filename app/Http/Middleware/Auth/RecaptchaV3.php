<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RecaptchaV3
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
                'recaptcha_key' => ['required','string', function(string $attribute, mixed $value, Closure $fails){
                    $response = Http::asForm()->post("https://www.google.com/recaptcha/api/siteverify", [
                        'secret' => config('services.recaptcha.secret_key'),
                        'response' => $value,
                        'remoteip' => request()->ip()
                    ]);
                    if(!$response->json('success')){
                        $fails("El recaptcha es invalido");
                    }
                } ],
            ]);

            if ($validator->fails()){
                return response()->json(['error' => $validator->errors()], 400);
            }

            return $next($request);
        } catch (QueryException $ex) {
            Log::error('Query error Middleware@DataChangePassword: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Credenciales invalidas.']], 400);
        } catch (\Exception $ex) {
            Log::error('unknown error Middleware@DataChangePassword: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Error en el servidor']], 500);
        }
    }
}
