<?php

namespace App\Http\Controllers\CRUD\ChargeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Charge;
use App\Models\PaymentMethod;;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('charge_id')){
            return $this->singleRecord($request->input('charge_id'));
        }else{
            return $this->allRecords($request->input('charge_ids'));
        }
    }

    public function singleRecord($id){
        try {
            $userId = auth()->id();
            $paymentMethod = Charge::where('id', $id)->firstOrFail();

            // Create a record in the Office table
            $paymentMethod->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '. $id], 200);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }

    public function allRecords($ids = null,$pagination=5, $sorters = [], $keyword =null, $typeKeyword = null){
        try {
            $userId = auth()->id();

            Charge::whereIn('id', $ids)->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '.join(',',$ids)], 200);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }
}
