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
    public function resource(Request $request)
    {
        if ($request->has('product_id') && $request->has('warehouse_id'))
            return $this->getInventory($request->input('product_id'), $request->input('warehouse_id'));
        if ($request->has('inventory_trade_id')) {
            return $this->singleRecord($request->input('inventory_trade_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = InventoryTrade::with(['supplier' => function ($query) {
                $query->select('id', 'commercial_registry', 'third_id');
                $query->with('third:id,business_name');
            }, 'inventories' => function ($query) {
                $query->with(['product' => function ($query) {
                    $query->with(['measure:id,symbol', 'brand:id,name']);
                    $query->select('products.id', 'products.name', 'products.consecutive', 'products.product_code', 'products.brand_id', 'products.measure_id', 'products.cost as defaultCost');
                }, 'warehouse' => function ($query) {
                    $query->with('city:id,name');
                    $query->select('warehouses.id', 'warehouses.address', 'warehouses.city_id');
                }]);
                $query->select('inventories.id', 'inventories.product_id', 'inventories.warehouse_id');
            }])
                ->where('inventory_trades.id', $id)
                ->first();
            $data['inventories']->map(function ($inventory) {
                $inventory['product']['cost'] = $inventory['pivot']['cost'];
                $inventory['product']['amount'] = $inventory['pivot']['amount'];
                unset($inventory['pivot']);
                return $inventory;
            });
            $data['supplier']['supplier'] = $data['supplier']['third']['business_name'];
            if (count($data['inventories']) > 0 && $data['inventories'][0]['warehouse']) {
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

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = InventoryTrade::with(['supplier' => function ($query) {
                $query->select('id', 'commercial_registry', 'third_id');
                $query->with('third:id,business_name,names,surnames,identification,type_document');
            }, 'inventories' => function ($query) {
                $query->select('inventory_id', 'inventory_trade_id');
            }]);

            //filter query with keyword 🚨
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'supplier':
                        $data = $data->whereHas('supplier',function($query) use ($filter){
                            $query->whereHas('third',function($query2) use($filter){
                                $query2->whereRaw(
                                    "UPPER(CONCAT(IFNULL(thirds.names, ' '), ' ', IFNULL(thirds.surnames, ' '), ' ',IFNULL(thirds.identification, ' '), ' ',IFNULL(thirds.business_name,' '))) LIKE ?",
                                    ['%' . strtoupper($filter['value']) . '%']
                                );
                            });
                        });

                        break;
                    case 'transaction_type':
                        $data = $data->whereIn('transaction_type',  $filter['value']);
                        break;
                    // case 'status':
                    //     $data = $data->whereIn('status', $filter['value']);
                    //     break;
                    default:
                        $data = $data->where('id', 'LIKE', '%' . $filter['value'] . '%');
                        break;
                }
            }

            //append shorters to query
            foreach ($sorters as $shorter) {
                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            if ($format == 'short') {
                $data = $data->where('status', 'A')->take(10)->get();
            } else {

                $data = $data->paginate($pagination);
                // $transformedData = $data->getCollection()->map(function($item){
                //     $total  = 0;
                //     $amount = 0;
                //     $item['inventories']->each(function($inventory) use (&$total, &$amount){
                //         $total += $inventory['pivot']['cost'] * $inventory['pivot']['amount'];
                //         $amount += $inventory['pivot']['amount'];
                //     });
                //     $item['total'] = $total;
                //     $item['amount'] = $amount;
                //     return $item;
                // });

                // $data->setCollection($transformedData);
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
    protected function getInventory($productId, $warehouseId)
    {
        try {
            $data = Inventory::where('product_id', $productId)->where('warehouse_id', $warehouseId)->first();
            return response()->json(['message' => 'read', 'data' => $data ?? 0], 200);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
