<?php

namespace App\Http\Controllers\CRUD\InventoryParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Third;
use App\Models\Client;
use App\Http\Utils\FileFormat;
use App\Models\Inventory;
use App\Models\InventoryTrade;
use App\Models\Product;
use App\Models\Service;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            Log::info('date');
            Log::info($request->input('date'));
            $inventoryTrade = InventoryTrade::create([
                    'transaction_type' => $request->input('transaction_type'),
                    'purpose' => $request->input('purpose'),
                    'note' => $request->input('note'),
                    'transaction_date' => $request->input('date'),
                    'supplier_id' => $request->input('supplier_id'),
                    'users_id' => $userId,
                    'status' => 'A'
                ]);
            foreach ($request['products'] as $value) {
            if(Inventory::where('product_id', $value['product_id'])->where('warehouse_id',$request['warehouse_id'])->get()->count() == 0){
                $inventory = Inventory::create([
                    'stock' => $value['amount'],
                    'product_id' => $value['product_id'],
                    'warehouse_id' => $request['warehouse_id'],
                    'status' => 'A',
                    'users_id' => $userId
                ]);

            }else{
                $inventory = Inventory::where('product_id', $value['product_id'])
                    ->where('warehouse_id',$request['warehouse_id'])
                    ->first();
                $inventory->update([
                    'stock' => $inventory['stock'] + $value['amount'],
                    'status' => 'A',
                    'users_update_id' => $userId
                ]);
            }
            $inventoryTrade->inventories()->attach($inventory['id'], [
                'cost' => $value['cost'],
                'amount' => $value['amount'],
                'users_id' => $userId,
                'status' => 'A'
            ]);
            }
            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error ClientResource@createResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error ClientResource@createResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
