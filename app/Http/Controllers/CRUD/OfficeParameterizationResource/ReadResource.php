<?php

namespace App\Http\Controllers\CRUD\OfficeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Office;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('office_id')) {
            return $this->singleRecord($request->input('office_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Office::where('id', $id)
                ->firstOrFail(['id', 'name', 'address', 'phone', 'city_id', 'status']);
            // Create a record in the Office table
            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters=[], $format = null)
    {
        try {
            if ($format == 'short') {
                $data = Office::select('id', 'name')->get();
            } else {
                $data = Office::select('id', 'name', 'address', 'phone', 'status', 'city_id','updated_at')
                    ->with('city:id,name');
                //filter query with keyword 🚨
                if ($filters) {
                foreach ($filters as $filter) {
                    switch ($filter['key']) {
                        case 'name':
                            $data = $data->whereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                            break;
                        case 'status':
                            $data = $data->WhereIn('status', $filter['value']);
                            break;
                        case 'address':
                            $data = $data->orWhere('address','LIKE', '%' . $filter['value'] . '%');
                        default:
                            $data = $data->orWhere('id','LIKE', '%' . $filter['value'] . '%');
                            break;
                    }
                }
                }
                //append shorters to query
                foreach ($sorters as $shorter) {
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
