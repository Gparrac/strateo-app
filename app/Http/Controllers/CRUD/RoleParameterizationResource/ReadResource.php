<?php

namespace App\Http\Controllers\CRUD\RoleParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Form;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReadResource implements CRUD, RecordOperations
{

    public function resource(Request $request)
    {
        if ($request->has('role_id')) {
            return $this->singleRecord($request->input('role_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        $role = Role::find($id);
        $formsRecord =  Form::leftjoin('sections', 'forms.section_id', 'sections.id')->select('forms.id', 'forms.name', 'sections.name as section_name')->get();
        $forms = [];
        foreach ($formsRecord as $key => $value) {
            $form = ['form_id' => $value['id'], 'section' => $value['section_name'] ?? '', 'permissions_id' => []];
            $permissions =
                Permission::join('permission_roles', 'permission_roles.permission_id', 'permissions.id')
                ->where('permission_roles.status', 'A')
                ->where('permission_roles.form_id', $value['id'])
                ->where('permission_roles.role_id', $id)->select('permissions.id')->get();;
            if ($permissions->isNotEmpty()) {
                // return response()->json(['message' => 'Read: ' . $id, 'data' => $permissions], 200);
                $form['permissions_id'] = clone $permissions->pluck('id');
            }

            array_push($forms, $form);
        }
        $role['forms'] = $forms;
        return response()->json(['message' => 'Read: ' . $id, 'data' => $role], 200);
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            if ($format == 'short') {
                $data = Role::where('status', 'A')->select('id', 'name')->get();
            } else {

                $data = Role::withCount([
                    'permissions' => function ($query) {
                        $query->where('permission_roles.status', 'A');
                    }, 'users' => function ($query) {
                        $query->where('users.status', 'A');
                    }
                ]);
                if ($filters) {
                    foreach ($filters as $filter) {
                        switch ($filter['key']) {
                            case 'name':
                                $data = $data->whereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                                break;
                            case 'status':
                                $data = $data->whereIn('status', $filter['value']);
                                break;
                            default:
                                $data = $data->Where('id', 'LIKE', '%' . $filter['value'] . '%');
                                break;
                        }
                    }
                }
                //append shorters to query
                foreach ($sorters as $key => $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }
            return response()->json(['message' => 'Read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
