<?php

namespace App\Http\Controllers\CRUD\RoleParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD
{
    public function resource(Request $request)
    {
        //roles_id
        $userId = Auth::id(); //meanwhile implement auth module
        try {
                // DB::table('permission_roles')->whereIn('role_id', $request['roles_id'])->update([
                //     'status' => 'I',
                //     'users_update_id' => $userId
                // ]);
                Role::whereIn('id', $request['roles_id'])->update([
                    'status' => 'I',
                    'users_update_id' =>$userId
                ]);
            DB::commit();


            return response()->json(['message' => 'Usuarios eliminados correctamente']);
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();
            Log::error('unknown error UsersParameterization@createResource: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar usuarios'], 500);
        }
    }

}
