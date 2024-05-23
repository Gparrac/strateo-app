<?php

namespace App\Http\Controllers\CRUD\PurchaseOrderParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class ReadResource implements CRUD, RecordOperations
{
    private $format;
    public function resource(Request $request)
    {
        if ($request->has('purchase_order_id')) {
            return $this->singleRecord($request->input('purchase_order_id'));
        } else {
            $this->format = $request->input('format');
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = PurchaseOrder::where('id', $id)
                ->select('id', 'supplier_id', 'date', 'note', 'status')
                ->with(['supplier' => function ($query) {
                    $query->with(['third' =>
                    function ($query) {
                        $query->select(['id', DB::raw('IFNULL(names, business_name) as supplier'), 'type_document', 'identification']);
                    }])->select('suppliers.id', 'suppliers.commercial_registry', 'suppliers.third_id');
                }, 'products' => function ($query) {
                    $query->with(['measure:id,symbol', 'brand:id,name']);
                    $query->select('products.id', 'products.name', 'products.consecutive', 'products.product_code', 'products.brand_id', 'products.measure_id', 'products.cost as defaultCost');
                }])
                ->first();
            $data->products->each(function ($product) {
                $product['amount'] = $product['pivot']['amount'];
                unset($product['pivot']);
            });
            $data['supplier']['supplier'] = $data['supplier']['third']['supplier'];


            unset($data['supplier']['third']);


            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrder@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrder@read:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = new PurchaseOrder();
            //filter query with keyword 🚨
            //filter query with keyword 🚨
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'supplier':
                        $data->whereHas('supplier', function ($query) use ($filter) {
                            $query->whereHas('third', function ($query) use ($filter) {
                                $query->where('UPPER(CONCAT(names," ",surnames," ",identification))', 'LIKE', '%' . strtoupper($filter['value']) . '%');
                            });
                        });
                        break;
                    case 'status':
                        $data = $data->whereIn('status', $filter['value']);
                        break;
                    default:
                        $data = $data->where('id', 'LIKE', '%' . $filter['value'] . '%');
                        break;
                }
            }
            if ($format == 'short') {
                $data = $data->where('status', 'A')->select('id', 'supplier_id', 'date', 'note')->with('supplier')->take(10)->get();
            } else {
                $data = $data->with(['supplier' => function ($query) {
                    $query->with(['third' =>
                    function ($query) {
                        $query->select(['id', 'names', 'surnames','business_name', 'type_document', 'identification']);
                    }]);
                    $query->select('suppliers.id', 'suppliers.third_id');
                }]);
                //append shorters to query
                foreach ($sorters as $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }

            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error PurchaseOrder@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error PurchaseOrder@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
