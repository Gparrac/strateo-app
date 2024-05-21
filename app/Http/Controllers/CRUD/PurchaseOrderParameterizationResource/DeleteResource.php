<?php

namespace App\Http\Controllers\CRUD\PurchaseOrderParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD
{
    public function resource(Request $request)
    {
        $userId = Auth::id(); //meanwhile implement auth module
        $ids = $request->input('purchase_order_ids');
        PurchaseOrder::whereIn('id', $ids)->update([
            'status' => 'I',
            'users_update_id' => $userId
        ]);
        try {
            return response()->json(['message' => 'Not Deleted: '. implode('- ', $ids) ], 200);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrder@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrder@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }


}
