<?php

namespace App\Http\Controllers\CRUD\InventoryParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryTrade;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ReadResource implements CRUD, RecordOperations
{
    private $format;
    public function resource(Request $request)
    {
        if ($request->has('inventory_trade_id')) {
            return $this->singleRecord($request->input('inventory_trade_id'));
        } else {
            $this->format = $request->input('format');
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = InventoryTrade::with(['supplier' => function($query){
                $query->select('id','commercial_registry','third_id');
                $query->with('third:id,business_name');
            }, 'inventories' => function($query){
                $query->with(['product' => function($query){
                    $query->with(['measure:id,symbol','brand:id,name']);
                    $query->select('products.id','products.name','products.consecutive','products.product_code','products.brand_id','products.measure_id', 'products.cost as defaultCost');

                },'warehouse' => function($query){
                    $query->with('city:id,name');
                    $query->select('warehouses.id','warehouses.address','warehouses.city_id');
                }]);
                $query->select('inventories.id','inventories.product_id','inventories.warehouse_id');
            }])
            ->where('inventory_trades.id', $id)
                ->first();
            $data['inventories']->map(function($inventory){
                $inventory['product']['cost'] = $inventory['pivot']['cost'];
                $inventory['product']['amount'] = $inventory['pivot']['amount'];
                unset($inventory['pivot']);
                return $inventory;
            });
            $data['supplier']['supplier'] = $data['supplier']['third']['business_name'];
            if(count($data['inventories']) > 0 && $data['inventories'][0]['warehouse']){
                $data['warehouse'] = $data['inventories'][0]['warehouse'];
            }
            unset($data['supplier']['third']);


            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null)
    {
        try {
            $data = InventoryTrade::with(['supplier' => function($query){
                $query->select('id','commercial_registry','third_id');
                $query->with('third:id,business_name');
            }],['inventories' => function($query){
                $query->select('inventory_id','inventoy_trades_id', DB::raw('sum(amount) as total_cost'));
            }])->withCount('inventories');

            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
            }
            if ($this->format == 'short') {
                $data = $data->take(10)->get();

                $data->map(function ($service) {

                    return $service;
                });
            } else {

                //append shorters to query
                foreach ($sorters as $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }
            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
