<?php

namespace App\Http\Controllers\CRUD\RoleParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        //datos entrada [role_id=>roleId,forms=>[forms=> form_id, permissions_id => [1,2,..]]
        Log::info('entrando');
        DB::beginTransaction();
        try {
            $userId = Auth::id(); //meanwhile implement auth module
            $adminRole =false;// $request['role_id'] == 1 ?  true : false;

            Role::find($request['role_id'])->update([
                'name' =>  $request['name'],
                'description' => $request['description'],
                'users_update_id' => $userId,
            ]);
            if(!$adminRole){
                Log::info('paso1');
                Log::info($request['forms']);
            foreach ($request['forms'] as $key => $form) {
                if (isset($request['forms'][$key]['permissions_id'])) {
                    DB::table('permission_roles')->where('role_id', $request['role_id'])->where('form_id', $form['form_id'])->whereNotIn('permission_id', $form['permissions_id'])->update([
                        'status' => 'I'
                    ]);

                    foreach ($form['permissions_id'] as $key => $permission_id) {
                        $query = DB::table('permission_roles')->where('role_id', $request['role_id'])->where('form_id', $form['form_id'])->where('permission_id', $permission_id);

                        if ($query->count() == 0) {

                            Role::find($request['role_id'])->permissions()->attach($permission_id, [
                                'status' => 'A',
                                'form_id' => $form['form_id'],
                                'users_id' => $userId, //meanwhile implement auth module
                                'users_update_id' => $userId, //meanwhile implement auth module
                            ]);
                        } else {
                            $query->update([
                                'status' => 'A'
                            ]);
                        }
                    }
                } else {
                    Log::info('paso2');
                    DB::table('permission_roles')->where('role_id', $request['role_id'])->where('form_id', $form['form_id'])->update([
                        'status' => 'I'
                    ]);
                }
            }
        }
            DB::commit();
        } catch (QueryException $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('Query error RolesParameterization@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('unknown error RolesParameterization@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }

        return response()->json(['message' => 'Update'], 200);
    }
}
