<?php

namespace App\Http\Controllers\CRUD\SupplierParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ReadResource implements CRUD, RecordOperations
{
    private $format;
    public function resource(Request $request)
    {
        if ($request->has('supplier_id')) {
            return $this->singleRecord($request->input('supplier_id'));
        } else {
            $this->format = $request->input('format');
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Supplier::with(['fields' => function ($query) {
                $query->select('fields.id', 'fields.name', 'fields.type', 'fields.length');
            }, 'services' => function ($query) {
                $query->wherePivot('status', 'A')->select('services.id', 'services.name', 'services.description');
            }, 'third' => function ($query) {
                $query->with(['secondaryCiius:id,code,description', 'ciiu:id,code,description']);
                $query->select('id', 'type_document', 'identification', 'code_ciiu_id', 'verification_id', 'names', 'surnames', 'business_name', 'address', 'mobile', 'email', 'email2', 'postal_code', 'city_id');
            }])
                ->where('suppliers.id', $id)
                ->select('suppliers.id','suppliers.status', 'suppliers.note', 'suppliers.note', 'suppliers.third_id', 'suppliers.commercial_registry', 'suppliers.commercial_registry_file', 'suppliers.rut_file')
                ->first();
            $data->services->map(function ($service) use ($data) {

                $service['fields'] = Service::find($service['id'])->fields()
                    ->select('fields.id', 'fields.name', 'fields.type', 'fields.length', DB::raw('null as data'))
                    ->get()->map(function ($field) use ($data) {
                        $data['fields']->map(function ($dfield, $key) use ($field, $data) {
                            if ($field['id'] == $dfield['id']) {

                                ($field['type']['id'] == 'F') ? $field['pathFile'] = $dfield->pivot['path_info'] : $field['data'] = $dfield->pivot['path_info'];
                                unset($data['fields'][$key]);
                            }
                            return $dfield;
                        });
                        $field['required'] = $field->pivot['required'];
                        unset($field->pivot);
                        return $field;
                    });
                unset($service->pivot);
                return $data;
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

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null)
    {
        try {
            $data = Supplier::with(['third' =>
            function ($query) {
                $query->select(['id', DB::raw('IFNULL(names, business_name) as supplier'), 'type_document', 'identification']);
            }])->withCount(['services', 'fields']);
            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                if ($typeKeyword == 'name') {
                    $data = $data->whereHas('third', function ($query) use ($keyword) {
                        $query->where('names', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('names', 'LIKE', '%' . $keyword . '%');
                    });
                }else{
                    $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
                }
            }
            if($this->format == 'short'){
                $data = $data->select('suppliers.id', 'suppliers.commercial_registry','suppliers.third_id')->take(10)->get()->map(function($supplier){
                    $supplier['supplier'] = $supplier['third']['supplier'];
                    unset($supplier['third']);
                    return $supplier;
                });

            }else{
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
