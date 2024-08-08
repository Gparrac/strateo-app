<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Third;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class RecoveryPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        DB::beginTransaction();
        try{
        $email = $request->input('email');
        $third = Third::where('email', $email)->first();

        if ($third) {
            $token = Str::random(60);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => $token, 'created_at' => Carbon::now()]
            );
            $resetLink = config('app.front_redirect_url'). '/change-recovery-password?token=' . $token . '&email=' . urlencode($email);

            Mail::send('mail.passwordRecovery', ['link' => $resetLink], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Recuperación de contraseña.');
            });
            DB::commit();
        }
        return response()->json(['message' => 'Successful']);
    } catch (QueryException $ex) {
        DB::rollback();
        Log::error('Query error Middleware@RecoveryPasswordSendEmail: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
        return response()->json(['error' => ['auth' => 'Credenciales invalidas.']], 400);
    } catch (\Exception $ex) {
        DB::rollback();
        Log::error('unknown error Middleware@RecoveryPasswordSendEmail: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
        return response()->json(['error' => ['auth' => 'Error en el servidor']], 500);
    }
    }
    public function reset(Request $request)
    {
        DB::beginTransaction();
        try{
            $email = $request->input('email');
            $token = $request->input('token');
            $passwordReset = DB::table('password_reset_tokens')->where('email', $email)->where('token', $token)->first();

            if (!$passwordReset || Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
                return response()->json(['error' => ['fields' => 'El token para el cambio de contraseña es invalido!']], 400);
            }

            $third = Third::where('email', $email)->first();
            $user = $third->user;

            $user->forceFill([
                'password' => Hash::make($request->input('password')),
                'remember_token' => Str::random(60),
            ])->save();

            DB::table('password_reset_tokens')->where('email', $email)->delete();

            // Invalidate all active tokens if using Passport
            $user->tokens->each(function ($token) {
                $token->revoke();
            });
        DB::commit();
        return response()->json(['message' => 'Successful']);
    } catch (QueryException $ex) {
        DB::rollback();
        Log::error('Query error Middleware@resetPassword: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
        return response()->json(['error' => ['auth' => 'Credenciales invalidas.']], 400);
    } catch (\Exception $ex) {
        DB::rollback();
        Log::error('unknown error Middleware@resetPassword: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
        return response()->json(['error' => ['auth' => 'Error en el servidor']], 500);
    }
    }
}
