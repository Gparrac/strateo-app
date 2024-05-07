<?php

namespace App\Http\Controllers\CRUD\TaxValueParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tax;
use App\Models\TaxValue;

class ReadResource implements CRUD, RecordOperations
{

    public function resource(Request $request)
    {
        if ($request->has('tax_value_id')) {
            return $this->singleRecord($request->input('tax_value_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = TaxValue::where('id', $id)->select('id', 'percent')->first();
            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error TaxResource@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error TaxResource@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = new TaxValue();
            if ($format == 'short') {
                $data = $data->select('id','percent')->take(10)->get();
            } else {
                $data = $data->with('taxes:id,acronym')->select('id','percent');
                //append shorters to query
                foreach ($sorters as $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }



            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error TaxResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error TaxResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
