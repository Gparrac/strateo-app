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
    //datos entrada [role_id=>roleId,forms=>[forms=> form_id, permissions_id => [1,2,..]]
    foreach ($request['forms'] as $key => $form) {
        foreach ($form['permissions_id'] as $key => $permission) {
            Log::info($form['formId']);
            Role::find($request->roleId)->permissions()->attach($permission,[
                'status'=> 'A',
                'form_id'=> $form['formId'],
                'users_id' => Auth::id() || 1, //meanwhile implement auth module
                'users_update_id' => Auth::id() || 1, //meanwhile implement auth module
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
