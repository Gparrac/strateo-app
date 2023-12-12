<?php

namespace App\Http\Middleware\CRUD\UserParameterization;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\CRUD\ValidateDataMiddlewareContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserParameterization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info($request);
        $validationPermissions = false;
        $query = DB::table('forms')
        ->join('permission_roles','forms.id','=','permission_roles.form_id')
        ->join('roles','permission_roles.role_id','=','roles.id')
        ->join('permissions','permission_roles.permission_id','=','permissions.id')
        ->join('users','roles.id','users.role_id')
        ->where('permission_roles.status','A')
        ->where('users.id',Auth::id() || 1)->where('forms.table','users');
        Log::info($query->count());
            switch($request->method()){
                case 'POST':
                    if($query->where('permissions.name','GUARDAR')->count() == 0){
                    $validationPermissions = true;
                    break;
                    }

                    $strategy = new ValidateDataMiddlewareContext(new CreateMiddleware());
                    break;
                case 'GET':
                    if($query->where('permissions.name','CONSULTAR')->count() == 0){
                        $validationPermissions = true;
                        break;
                        }
                    $strategy = new ValidateDataMiddlewareContext(new ReadMiddleware());
                    break;
                case 'PUT':
                    if($query->where('permissions.name','ACTUALIZAR')->count() == 0){
                        $validationPermissions = true;
                        break;
                        }
                    $strategy = new ValidateDataMiddlewareContext(new UpdateMiddleware());
                    break;
                case 'DELETE':
                    if($query->where('permissions.name','ELIMINAR')->count() == 0){
                        $validationPermissions = true;
                        break;
                        }
                    $strategy = new ValidateDataMiddlewareContext(new DeleteMiddleware());
                    break;
                default:
                    return response()->json(['error' => 'Method not allowed'], 400);
            }
            if($validationPermissions ) return response()->json(['error' => 'insufficient privileges'], 403);
            $execValidate = $strategy->execValidate($request);
            if($execValidate['error']) return response()->json(['error' => $execValidate['message']], 400);



        return $next($request);
    }
}
