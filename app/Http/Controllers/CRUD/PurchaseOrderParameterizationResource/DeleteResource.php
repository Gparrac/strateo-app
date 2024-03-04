<?php

namespace App\Http\Controllers\CRUD\PurchaseOrderParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('purchase_order_id')){
            return $this->singleRecord($request->input('purchase_order_id'));
        }else{
            return $this->allRecords($request->input('purchase_order_ids'));
        }
    }

    public function singleRecord($id){
        try {
            return response()->json(['message' => 'Not Deleted: '. $id], 200);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrder@delete:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrder@delete:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }

    public function allRecords($ids = null,$pagination=5, $sorters = [], $keyword =null, $typeKeyword = null, $format = null){
        try {
            return response()->json(['message' => 'Not Deleted: '. $ids], 200);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrder@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrder@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }
}
