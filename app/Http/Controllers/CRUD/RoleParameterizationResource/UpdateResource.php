<?php

namespace App\Http\Controllers\CRUD\RoleParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        //datos entrada [roleId=>roleId,forms=>[formId=> formId, activePermissions => [1,2,..]]
        // DB::table('permission_roles')->where('role_id',$request->roleId)->update([
        //     'status'=>'I'
        // ]);
        foreach ($request->forms as $key => $form) {
            $query = DB::table('permission_roles')->where('role_id',$request->roleId)->where('form_id',$form->formId);
            $query->whereNoIn('permission-id',$form->activePermissions)->update([
                'status' => 'I'
            ]);
            foreach ($form->activePermissions as $key => $permission) {
                $query = $query->where('permission_id',$permission);
                if($query->count() == 0){
                    $query->create([
                        'permission_id'=> $permission,
                        'status'=> 'A',
                        'form_id'=> $form->formId,
                        'user_id' => Auth::id(),
                        'users_update_id' => Auth::id(),
                    ]);
                }else{
                    $query->update([
                        'status' => 'A'
                    ]);
                }

            }
        }

        return response()->json(['message' => 'Update'], 200);
    }
}
