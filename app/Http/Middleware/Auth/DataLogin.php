<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Custom\Error\ProcessErrors;
use App\Models\Third;
use App\Models\User;

class DataLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required_without:identification|string|email',
                'identification' => 'required_without:email|min:5|max:12|exists:thirds,identification',
                'password' => 'required|string',
            ]);

            if ($validator->fails()){
                return response()->json(['error' => $validator->errors()], 400);
            }

            //Get email or identification
            $email = $request->input('email');
            $identification = $request->input('identification');

            if($email && $identification){
                return response()->json(['error' => ['fields' => 'too much fields for request']], 400);
            }

            $third = Third::where('email', $email)
                ->orWhere('identification', $identification)
                ->first();
            if(!$third){
                return response()->json(['error' => ['auth' => 'Credenciales invalidas.']], 400);
            }

            $user = $third->user;
            if (!$user || !password_verify($request->input('password'), $user->password)) {
                return response()->json(['error' => ['auth' => 'Credenciales invalidas.']], 400);
            }
            if ($user->status != 'A') {
                return response()->json(['error' => ['auth' => 'Usuario desactivado']], 400);
            }

            $request->merge(['user' => $user, 'third' => $third]);

            return $next($request);
        } catch (QueryException $ex) {
            Log::error('Query error Middleware@DataLogin: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Credenciales invalidas.']], 400);
        } catch (\Exception $ex) {
            Log::error('unknown error Middleware@DataLogin: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['error' => ['auth' => 'Error en el servidor.']], 500);
        }
    }

}
