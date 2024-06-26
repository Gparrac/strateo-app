<?php

namespace App\Http\Controllers\CRUD\ServiceParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('service_id')) {
            return $this->singleRecord($request->input('service_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Service::with(['fields' => function ($query) {
                $query->where('fields_services.status', '=', 'A')->select('fields.id', 'fields.name', 'type', 'length', 'fields.status');
            }])
                ->where('services.id', $id)
                ->first();

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
            $data = new Service();
            //filter query with keyword 🚨
            if ($filters) {
                foreach ($filters as $filter) {
                    switch ($filter['key']) {
                        case 'name':
                            $data = $data->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($filter['value']) . '%']);
                            break;
                        case 'status':
                            $data = $data->whereIn('status', $filter['value']);
                            break;
                        default:
                            $data = $data->whereRaw('UPPER(id) LIKE ?', ['%' . strtoupper($filter['value']) . '%']);

                            break;
                    }
                }
            }
            if ($format == 'short') {
                $data = $data->with(['fields' => function ($query) {
                    $query->select('fields.id', 'fields.name', 'fields.type', 'fields.length');
                    $query->where('fields_services.status', '=', 'A');
                }])->select('services.id', 'services.name', 'services.description')->take(10)->get();

                $data->map(function ($service) {
                    $service->fields->each(function ($field) {
                        $field['data'] = null;
                        $field['required'] = $field['pivot']['required'];
                        unset($field->pivot);
                    });
                    return $service;
                });
            } else {
                $data = $data->with('fields:id,name,type,length,status')->withCount(['fields' => function ($query) {
                    $query->where('fields_services.status', '=', 'A');
                }])->withCount(['suppliers', 'fields', 'employees']);
                //append shorters to query
                foreach ($sorters as $shorter) {
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
