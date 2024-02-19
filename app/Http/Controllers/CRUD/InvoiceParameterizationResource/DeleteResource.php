<?php

namespace App\Http\Controllers\CRUD\InvoiceParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Invoice;
use Illuminate\Http\Request;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('invoice_id')){
            return $this->singleRecord($request->input('invoice_id'));
        }else{
            return $this->allRecords($request->input('invoice_ids'));
        }
    }

    public function singleRecord($id){
        try {
            $userId = auth()->id();
            $invoice = Invoice::where('id', $id)->firstOrFail();
            // Create a record in the Office table
            $invoice->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '. $id], 200);
        } catch (QueryException $ex) {
            Log::error('Query error InvoiceResource@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error InvoiceResource@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }

    public function allRecords($ids = null,$pagination=5, $sorters = [], $keyword =null, $typeKeyword = null,$format=null){
        try {
            $userId = auth()->id();

            Invoice::whereIn('id', $ids)->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '.join(',',$ids)], 200);
        } catch (QueryException $ex) {
            Log::error('Query error InvoiceResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error InvoiceResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }
}
