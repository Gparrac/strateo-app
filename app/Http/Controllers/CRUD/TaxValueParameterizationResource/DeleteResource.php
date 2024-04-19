<?php

namespace App\Http\Controllers\CRUD\TaxValueParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;
use App\Models\Tax;
use App\Models\TaxValue;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('tax_value_id')){
            return $this->singleRecord($request->input('tax_value_id'));
        }else{
            return $this->allRecords($request->input('tax_value_ids'));
        }
    }

    public function singleRecord($id){
        try {
            // $userId = auth()->id();

            // $tax = TaxValue::where('id', $id)->firstOrFail();
            // // Create a record in the Office table
            // $tax->update([
            //     'status' => 'I',
            //     'users_update_id' => $userId,
            // ]);
            return response()->json(['message' => 'Delete: '. $id], 200);
        } catch (QueryException $ex) {
            Log::error('Query error TaxResource@delete:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error TaxResource@delete:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }

    public function allRecords($ids = null,$pagination=5, $sorters = [], $keyword =null, $typeKeyword = null, $format = null){
        try {
            // $userId = auth()->id();

            // // TaxValue::whereIn('id', $ids)->update([
            // //     'status' => 'I',
            // //     'users_update_id' => $userId,
            // // ]);
            return response()->json(['message' => 'Delete: '.join(',',$ids)], 200);
        } catch (QueryException $ex) {
            Log::error('Query error TaxResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error TaxResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }
}
