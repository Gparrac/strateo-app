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
        try
        {
            $userId = Auth::id() || 1; //meanwhile implement auth module
            $role = Role::create([
                'name' =>  $request['name'],
                'description' => $request['description'] ?? null,
                'users_id' => $userId,
                'users_update_id' => $userId,
            ]);

            //datos entrada [role_id=>roleId,forms=>[forms=> form_id, permissions_id => [1,2,..]]
            foreach ($request['forms'] as $key => $form) {
                if (isset($request['forms'][$key]['permissions_id'])) {
                    foreach ($form['permissions_id'] as $key => $permission) {
                        $role->permissions()->attach($permission, [
                            'status' => 'A',
                            'form_id' => $form['form_id'],
                            'users_id' => $userId,
                            'users_update_id' => $userId,
                        ]);
                    }
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
