<?php

namespace App\Http\Controllers\CRUD\RoleParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Role;
use App\Models\Third;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
try {
    //datos entrada [roleId=>roleId,forms=>[formId=> formId, activePermissions => [1,2,..]]
    foreach ($request->forms as $key => $form) {
        foreach ($form->activePermissions as $key => $permission) {
            Role::find($request->roleId)->permissions()->attach([
                'permission_id'=> $permission,
                'status'=> 'A',
                'form_id'=> $form->id,
                'user_id' => Auth::id(),
                'users_update_id' => Auth::id(),
            ]);
        }
    }
    DB::commit();
    return response()->json(['message' => 'Create'], 200);
} catch (QueryException $ex) {
    // In case of error, roll back the transaction
    DB::rollback();
    Log::error('Query error RolesParameterization@createResource: ' . $ex->getMessage());
    return response()->json(['message' => 'create q'], 500);
} catch (\Exception $ex) {
    // In case of error, roll back the transaction
    DB::rollback();
    Log::error('unknown error RolesParameterization@createResource: ' . $ex->getMessage());
    return response()->json(['message' => 'create u'], 500);
}


    }
}