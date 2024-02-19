<?php

namespace App\Http\Controllers\CRUD\ProductParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryTrade;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ReadResource implements CRUD, RecordOperations
{
    private $warehouseFilter;
    private $types;
    private $supply;
    private $typeContent;
    public function resource(Request $request)
    {
        if ($request->has('product_id')) {
            return $this->singleRecord($request->input('product_id'));
        } else {
            $this->types = $request->input('types') ?? null;
            $this->warehouseFilter = $request->input('warehouseFilter') ?? null;
            $this->typeContent = $request->input('typeContent') ?? null;
            $this->supply = $request->input('supply') ?? null;
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'), $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Product::with([

                'brand' => function ($query) {
                    $query->where('status', 'A')->select('id', 'name');
                }, 'measure' => function ($query) {
                    $query->where('status', 'A')->select('id', 'symbol');
                },
                'categories' => function ($query) {
                    $query->select('categories.id', 'categories.name');
                }, 'childrenProducts' => function ($query) {
                    $query->with([
                        'brand' => function ($query) {
                            $query->where('brands.status', 'A')->select('id', 'name');
                        }, 'measure' => function ($query) {
                            $query->where('measures.status', 'A')->select('id', 'symbol');
                        },
                        'categories' => function ($query) {
                            $query->where('categories.status', 'A')->select('categories.id', 'categories.name');
                        },
                    ]);
                    $query->select(
                        'products.id',
                        'products.brand_id',
                        'products.measure_id',
                        'products.consecutive',
                        'products.cost',
                        'products.description',
                        'products.name',
                        'products.product_code',
                        'products.barcode',
                        'products.size'
                    );
                },
                'taxes:id,name,acronym'
            ])->where('products.id', $id)
                ->select(
                    'products.id',
                    'products.brand_id',
                    'products.measure_id',
                    'products.consecutive',
                    'products.cost',
                    'products.description',
                    'products.name',
                    'products.product_code',
                    'products.size',
                    'products.status',
                    'products.type',
                    'products.type_content',
                    'products.barcode'
                )->first();

            $data['categories']->map(function ($category) {
                unset($category['pivot']);
                return $category;
            });
            $data->taxes->each(function ($tax) {
                $tax['porcent'] = $tax['pivot']['porcent'];
                unset($tax['pivot']);
                });
            $data['childrenProducts']->map(function ($product) {
                $product['amount'] = $product['pivot']['amount'];
                unset($product['pivot']);
                $product['categories']->map(function ($category) {
                    unset($category['pivot']);
                    return $category;
                });
                return $product;
            });


            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null, $format = null)
    {
        try {
            $data = new Product();
            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                    $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
            }
            if ($format == 'short') {
                $data = $data->with(['taxes:id,name,acronym',
                    'brand:id,name', 'measure:id,symbol',
                    'categories:id,name'
                ])->where('status', 'A');
                if($this->types) $data = $data->whereIn('type', $this->types);
                if($this->typeContent)  $data = $data->whereIn('type_content', $this->typeContent);
                if($this->supply)  $data = $data->where('type_content', $this->supply);
                $data = $data->select(
                    'products.id',
                    'products.size',
                    'products.brand_id',
                    'products.measure_id',
                    'products.id',
                    'products.name',
                    'products.consecutive',
                    'products.product_code',
                    'products.cost',
                    'products.barcode',
                    'products.type',
                    'products.type_content'
                )->take(10)->get();

                $data->map(function ($product)  {
                    if($this->warehouseFilter){
                        $inventory = Inventory::where('product_id', $product['id'])->where('warehouse_id', $this->warehouseFilter)->first();
                        $product['defaultCost'] = $product['cost'];
                        $product['stock'] = $inventory !== null ? $inventory['stock'] : 0;
                    }
                    $product->taxes->each(function ($tax) {
                        $tax['porcent'] = $tax['pivot']['porcent'];
                        unset($tax['pivot']);
                        });
                    $product->categories->each(function ($category) {
                        unset($category->pivot);
                    });
                    return $product;
                });
            } else {

                $data = $data->with(['brand:id,name', 'measure:id,symbol'])->withCount('categories')->withCount('childrenProducts');
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
