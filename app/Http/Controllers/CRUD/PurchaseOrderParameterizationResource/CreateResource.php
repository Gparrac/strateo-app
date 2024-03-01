<?php

namespace App\Http\Controllers\CRUD\PurchaseOrderParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
    
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->input('supplier_id'),
                'date' => $request->input('date'),
                'note' => $request->input('note'),
                'users_id' => $userId,
            ]);

            // Adjuntar productos a la orden de compra
            $products = $request->input('products');

            foreach ($products as $key => $value) {
                $purchaseOrder->products()->attach($value['id'], [
                    'amount' => $value['amount'],
                    'users_id' => $userId,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error PurchaseOrderResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error PurchaseOrderResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
