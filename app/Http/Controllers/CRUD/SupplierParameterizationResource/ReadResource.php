<?php

namespace App\Http\Controllers\CRUD\SupplierParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Http\Utils\FileFormat;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\DynamicService;
use App\Models\Field;
use App\Models\Service;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('supplier_id')) {
            return $this->singleRecord($request->input('supplier_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Supplier::with(['dynamicServices' => function ($query) {
                $query->with(['service:id,name,description', 'fields' => function ($query) {
                    $query->wherePivot('status', 'A')->select(['fields.id', 'fields.name', 'fields.type', 'fields.length']);
                }]);
                $query->where('status', 'A')->select('dynamic_services.id', 'service_id', 'supplier_id');
            }, 'third' => function ($query) {
                $query->with(['secondaryCiius:id,code,description', 'ciiu:id,code,description', 'city:id,name']);
                $query->select('id', 'type_document', 'identification', 'code_ciiu_id', 'verification_id', 'names', 'surnames', 'business_name', 'address', 'mobile', 'email', 'email2', 'postal_code', 'city_id');
            }])
                ->where('suppliers.id', $id)
                ->select('suppliers.id', 'suppliers.status', 'suppliers.note', 'suppliers.note', 'suppliers.third_id', 'suppliers.commercial_registry', 'suppliers.commercial_registry_file', 'suppliers.rut_file')
                ->first();
            $data['dynamicServices']->each(function ($ds, $dskey) use ($data) {
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
                $data['services'][$dskey] = $service;
            });
            unset($data->dynamicServices);
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
            $data = Supplier::with(['third' =>
            function ($query) {
                $query->select(['id', 'names', 'surnames', 'business_name', 'type_document', 'identification']);
            }])->withCount(['dynamicServices']);
            //filter query with keyword 🚨
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'third':
                        $data = $data->whereHas('third', function ($query) use ($filter) {
                            $query->whereRaw("UPPER(CONCAT(IFNULL(thirds.surnames,''),IFNULL(thirds.names,''),IFNULL(thirds.business_name,''))) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                        });
                        break;
                    case 'status':
                        $data = $data->whereIn('status', $filter['value']);
                        break;
                    default:
                        $data = $data->where('id', 'LIKE', '%' . $filter['value'] . '%');
                        break;
                }
            }
            // dd($data->get());

            if ($format == 'short') {
                $data = $data->where('status', 'A')->select('suppliers.id', 'suppliers.commercial_registry', 'suppliers.third_id')->take(10)->get()->map(function ($supplier) {

                    $supplier['supplier'] = $supplier['third']['fullname'];
                    $supplier['identification'] = $supplier['third']['fullid'];
                    return $supplier;
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
