<?php

namespace App\Http\Controllers\CRUD\EmployeeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Http\Utils\FileFormat;
use App\Models\DynamicService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\View\DynamicComponent;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('employee_id')) {
            return $this->singleRecord($request->input('employee_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Employee::with(['dynamicServices' => function ($query) {
                $query->with(['service:id,name,description', 'fields' => function ($query) {
                    $query->wherePivot('status', 'A')->select(['fields.id', 'fields.name', 'fields.type', 'fields.length']);
                }]);
                $query->where('status', 'A')->select('dynamic_services.id', 'service_id', 'employee_id');
            }, 'third' => function ($query) {
                $query->with('city:id,name');
                $query->select('id', 'type_document', 'identification', 'names', 'surnames', 'business_name', 'address', 'mobile', 'email', 'email2', 'postal_code', 'city_id');
            }, 'paymentMethods:id,name,description'])->where('employees.id', $id)
                ->select('employees.id', 'employees.status', 'employees.type_contract', 'employees.hire_date', 'employees.third_id', 'employees.end_date_contract', 'employees.rut_file', 'employees.resume_file')
                ->first();
            $data['paymentMethods']->map(function ($pm) {
                $pm['reference'] = $pm['pivot']['reference'];
                unset($pm['pivot']);
            });
            Log::info($data['DynamicServices']);
            $data['dynamicServices']->map(function ($ds, $dskey) use ($data) {
                Log::info('passing');
                $service = $ds['service'];

                $service['fields'] = Service::find($service['id'])->fields()
                    ->select('fields.id', 'fields.name', 'fields.type', 'fields.length', DB::raw('null as data'))
                    ->get()->map(function ($field) use ($ds) {
                        $ds['fields']->each(function ($dsfield) use ($field) {
                            if ($field['id'] == $dsfield['id']) {
                                ($field['type']['id'] == 'F' && $dsfield->pivot['path_info']) ? $field['pathFile'] = FileFormat::downloadPath($dsfield->pivot['path_info']) : $field['data'] = $dsfield->pivot['path_info'];
                            }
                        });
                        $field['required'] = $field->pivot['required'];
                        unset($field->pivot);
                        return $field;
                    });
                $data['dynamicServices'][$dskey] = $service;
            });
            unset($data->fields);
            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = Employee::with(['third' =>
            function ($query) {
                $query->select(['id', 'names', 'surnames', 'business_name', 'type_document', 'identification']);
            }])->withCount(['dynamicServices']);
            //filter query with keyword 🚨
            //filter query with keyword 🚨
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'third':
                        $data = $data->whereHas('third', function ($query) use ($filter) {
                            $query->whereRaw('UPPER(CONCAT(names," ",surnames)) LIKE ?', ['%' . strtoupper($filter['value']) . '%']);
                        });
                        break;
                    case 'status':
                        $data = $data->whereIn('status', $filter['value']);
                        break;
                    default:
                        $data = $data->orWhere('id', 'LIKE', '%' . $filter['value'] . '%');
                        break;
                }
            }
            if ($format == 'short') {
                $data = $data->where('status', 'A')->take(10)->get()->map(function ($query) {
                    return [
                        'id' => $query->id,
                        'fullname' => $query->third->names,
                        'identification' => $query->third->type_document . ': ' . $query->third->identification
                    ];
                });
            } else {
                //append shorters to query
                foreach ($sorters as  $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }
            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
