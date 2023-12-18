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
use Illuminate\Support\Facades\Log;

class ReadResource implements CRUD, RecordOperations
{

    private $format;
    public function resource(Request $request)
    {
        if ($request->has('role_id')) {
            return $this->singleRecord($request->input('role_id'));
        } else {
            $this->format = $request->input('format');
            return $this->allRecords();
        }
    }

    public function singleRecord($id)
    {
        $role = Role::find($id);
        $formsRecord =  Form::leftjoin('sections', 'forms.section_id', 'sections.id')->select('forms.id','forms.name', 'sections.name as section_name')->get();
        $forms = [];
        foreach ($formsRecord as $key => $value) {
            $form = ['form_id' => $value['id'], 'section' => $value['section_name'] ?? '', 'permissions_id' => []];
            $permissions =
                Permission::join('permission_roles','permission_roles.permission_id', 'permissions.id')
                ->where('permission_roles.status', 'A')
                ->where('permission_roles.form_id', $value['id'])
                ->where('permission_roles.role_id', $id)->select('permissions.id')->get();;
                if ($permissions->isNotEmpty()) {
                    Log::info($value['id']);
                    // return response()->json(['message' => 'Read: ' . $id, 'data' => $permissions], 200);
                $form['permissions_id'] = clone $permissions->pluck('id');
            }
            Log::info($form);

            array_push($forms, $form);
        }
        $role['forms'] = $forms;
        return response()->json(['message' => 'Read: ' . $id, 'data' => $role], 200);
    }

    public function allRecords($ids = null)
    {
        if ($this->format == 'short') {
            $data = Role::select('id', 'name')->get();
        } else {
            $data = Role::withCount([
                'permissions' => function ($query) {
                    $query->where('permission_roles.status', 'A');
                }, 'users' => function ($query) {
                    $query->where('users.status', 'A');
                }
            ],)->get();
        }

        return response()->json(['message' => 'Read', 'data' => $data], 200);
    }
}
