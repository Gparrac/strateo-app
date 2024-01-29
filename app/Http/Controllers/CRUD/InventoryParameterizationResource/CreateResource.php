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
use App\Models\Service;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            $inventoryTrade = InventoryTrade::create([
                    'transaction_type' => $request->input('transaction_type'),
                    'purpose' => $request->input('purpose'),
                    'note' => $request->input('note'),
                    'transaction_date' => $request->input('date'),
                    'supplier_id' => $request->input('supplier_id'),
                    'users_id' => $userId,
                    'further_discount' => $request['further_discount']
                ]);
            foreach ($request['products'] as $value) {
                $inventoryTrade->inventory()->attach($value['inventory_id'], [
                    'cost' => $value['cost'],
                    'amount' => $value['amount'],
                    'iva' => $value['iva'],
                    'ico' => $value['ico'],
                    'discount' => $value['discount'],
                    'users_id' => $userId,  $userId
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
