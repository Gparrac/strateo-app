<?php

namespace App\Http\Controllers\CRUD\PurchaseOrderParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tax;

class ReadResource implements CRUD, RecordOperations
{
    private $format;
    public function resource(Request $request)
    {
        if ($request->has('tax_id')) {
            return $this->singleRecord($request->input('tax_id'));
        } else {
            $this->format = $request->input('format');
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Tax::where('id', $id)->select('id', 'name', 'acronym', 'status', 'default_percent')->first();
            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrder@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrder@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null, $format = null)
    {
        try {
            $data = new Tax();
            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
            }
            if($format == 'short'){
                $data = $data->where('status','A')->select('id','acronym','name','default_percent')->take(10)->get();
            }else{

                //append shorters to query
                foreach ($sorters as $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }

            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrder@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrder@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
