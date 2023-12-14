<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleMiddleware
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
                //Third table
                'form_id' => 'required|exists:forms,id',
            ]);

            if ($validator->fails()){
                return response()->json(['error' => TRUE, 'message' => $validator->errors()]);
            }

            $userId = Auth::id();
            $query = Form::join('permission_roles','forms.id','=','permission_roles.form_id')
            ->join('roles','permission_roles.role_id','=','roles.id')
            ->join('permissions','permission_roles.permission_id','=','permissions.id')
            ->join('users','roles.id','users.role_id')
            ->where('permission_roles.status','A')
            ->where('users.id', $userId)
            ->where('forms.id', $request->input('form_id'));

            // dd($query->count());
            switch($request->method()){
                case 'POST':
                    $validationPermissions = $query->where('permissions.name','GUARDAR')->count() == 0;
                    break;
                    case 'GET':
                        $validationPermissions = $query->where('permissions.name','CONSULTAR')->count() == 0;
                        break;
                        case 'PUT':
                            $validationPermissions = $query->where('permissions.name','ACTUALIZAR')->count() == 0;
                            break;
                            case 'DELETE':
                                $validationPermissions = $query->where('permissions.name','ELIMINAR')->count() == 0;
                                break;
                                default:
                                return response()->json(['error' => 'Method not allowed'], 400);
                            }

            if($validationPermissions) return response()->json(['error' => 'insufficient privileges'], 403);

            return $next($request);
        } catch (QueryException $ex) {
            Log::error('Query error RoleMiddleware: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'role q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error RoleMiddleware: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'role u'], 500);
        }
    }
}
