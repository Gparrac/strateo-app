<?php

namespace App\Http\Controllers\CRUD\RoleParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Form;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Third;
use App\Models\User;
use Illuminate\Http\Request;

class ReadResource implements CRUD, RecordOperations
{

    private $format;
    public function resource(Request $request)
    {
        if($request->has('role_id')){
            return $this->singleRecord($request->input('role_id'));
        }else{
            $this->format = $request->input('format');
                return $this->allRecords();
        }
    }

    public function singleRecord($id){

        $role = Role::find($id);
        $forms = [];
        foreach (Form::all()->pluck
        ('id') as $key => $value) {
            $form = ['form_id'=>$value, 'permissions_id'=>[]];
            $query = Role::with(['permissions' => function ($query) use ($value) {
                $query->where('permission_roles.status','A')->where('permission_roles.form_id',$value);
            }])->where('roles.id',$id)->get();
            if($query->isNotEmpty()){
                $form['permissions_id'] = $query->pluck('permissions')->flatten()->pluck('id');
            }
            // return response()->json(['message' => 'Read: '.$id, 'data' => $form], 200);
            array_push($forms,$form);
        }
        $role['forms'] = $forms;
        return response()->json(['message' => 'Read: '.$id, 'data' => $role], 200);
    }

    public function allRecords($ids = null){
        if($this->format == 'short'){
            $data = Role::select('id','name')->get();
        }else{
            $data = Role::withCount([
                'permissions' => function ($query) {
                    $query->where('permission_roles.status','A');
                },'users' => function ($query) {
                    $query->where('users.status','A');
                }
            ],)->get();
        }

        return response()->json(['message' => 'Read', 'data' => $data], 200);
    }

}
