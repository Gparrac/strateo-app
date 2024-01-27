<?php

namespace App\Http\Controllers\CRUD\MeasureParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Measure;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('measure_id')) {
            return $this->singleRecord($request->input('measure_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Measure::where('id', $id)->select('id', 'type', 'name', 'symbol', 'status')->first();

            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error MeasureResource@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error MeasureResource@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null)
    {
        try {
            $data = Measure::select('id', 'name', 'type', 'symbol', 'status');
            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
            }
            //append shorters to query
            foreach ($sorters as $key => $shorter) {
                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error MeasureResource@read:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error MeasureResource@read:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
