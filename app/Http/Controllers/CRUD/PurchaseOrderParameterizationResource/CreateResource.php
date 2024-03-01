<?php

namespace App\Http\Controllers\CRUD\PurchaseOrderParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tax;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = Auth::id();
            
            $validator = Validator::make($request->all(), [
                //Purchase order table
                'supplier_id' => 'required|exists:suppliers,id',
                'date' => 'required|date',
                'note' => 'required|string|min:3|max:45',
    
                //purchase_orders_products
                'products' => [
                    'required',
                    'array',
                    Rule::exists('products', 'id'),
                ],
                'amount' => 'required|numeric|digits_between:1,10',
            ]);
    
            Tax::create([
                'name' => $request->input('name'),
                'acronym' => $request->input('acronym'),
                'status' => $request->input('status'),
                'default_percent' => $request->input('default_percent'),
                'users_id' => $userId,
            ]);

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrderResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrderResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
