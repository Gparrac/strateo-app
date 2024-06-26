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
        try {
            if ($request->has('product_id')) {
                $data = $this->singleRecord($request->input('product_id'));
            } else {
                $this->types = $request->input('types') ?? null;
                $this->warehouseFilter = $request->input('warehouseFilter') ?? null;
                $this->typeContent = $request->input('typeContent') ?? null;
                $this->supply = $request->input('supply') ?? null;
                $data = $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
            }
            return response()->json(['message', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ProductResource@readResource - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ProductResource@readResource - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function singleRecord($id)
    {
        $data = Product::with([
            'taxes' => function ($query) {
                $query->with('taxValues:id,percent');
                $query->select('taxes.id', 'name', 'acronym', 'type');
            },
            'librettoActivities' => function ($query) {
                $query->where('libretto_activities_products.status', 'A')->select('libretto_activities.id', 'name', 'description');
            },
            'brand' => function ($query) {
                $query->where('status', 'A')->select('id', 'name');
            }, 'measure' => function ($query) {
                $query->where('status', 'A')->select('id', 'symbol');
            },
            'categories' => function ($query) {
                $query->select('categories.id', 'categories.name');
            }, 'subproducts' => function ($query) {
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
                    'products.size',
                );
            }
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
                'products.barcode',
                'products.tracing'
            )->first();

        $data['categories']->map(function ($category) {
            unset($category['pivot']);
            return $category;
        });
        $data->taxes->each(function ($tax) {
            $tax['percent'] = $tax['pivot']['percent'];
            unset($tax['pivot']);
        });
        $data['subproducts']->map(function ($product) {
            $product['amount'] = $product['pivot']['amount'];
            unset($product['pivot']);
            $product['categories']->map(function ($category) {
                unset($category['pivot']);
                return $category;
            });
            return $product;
        });
        return $data;
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = null, $format = null)
    {
        $data = new Product();
        //filter query with keyword 🚨

        foreach ($filters as $filter) {
            switch ($filter['key']) {
                case 'name':
                    $data = $data->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($filter['value']) . '%']);
                    break;
                case 'status':
                    $data = $data->whereIn('status', $filter['value']);
                    break;
                case 'non-subproducts':
                    $data = $data->whereDoesntHave('subproducts');
                    break;
                default:
                    $data = $data->where('id', 'LIKE', '%' . $filter['value'] . '%');
                    break;
            }
        }
        if ($format == 'short') {
            $data = $data->withCount('subproducts')->with([
                'taxes' => function ($query) {
                    $query->with('taxValues:id,percent');
                    $query->select('taxes.id', 'name', 'acronym', 'type');
                },
                'brand:id,name', 'measure:id,symbol',
                'categories:id,name',
                'subproducts' => function ($query) {
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
                        'products.consecutive',
                        'products.name',
                        'products.description',
                        'products.brand_id',
                        'products.product_code',
                        'products.barcode',
                        'products.type_content',
                        'products.type',
                        'products.tracing',
                        'products.size',
                        'products.measure_id',
                        'products.brand_id',

                    );
                }
            ])->where('status', 'A');
            if ($this->types) $data = $data->whereIn('type', $this->types);
            if ($this->typeContent)  $data = $data->whereIn('type_content', $this->typeContent);
            if ($this->supply)  $data = $data->where('type_content', $this->supply);
            $data = $data->select(
                'products.id',
                'products.size',
                'products.brand_id',
                'products.measure_id',
                'products.id',
                'products.name',
                'products.description',
                'products.consecutive',
                'products.product_code',
                'products.cost',
                'products.barcode',
                'products.type',
                'products.type_content'
            )->take(10)->get();

            $data->map(function ($product) {
                $product['defaultCost'] = $product['cost'];
                if ($this->warehouseFilter) {
                    $inventory = Inventory::where('product_id', $product['id'])->where('warehouse_id', $this->warehouseFilter)->first();
                    $product['stock'] = $inventory !== null ? $inventory['stock'] : 0;
                }
                $product->taxes->each(function ($tax) {
                    $tax['percent'] = $tax['pivot']['porcent'];

                    unset($tax['pivot']);
                });
                $product->categories->each(function ($category) {
                    unset($category->pivot);
                });
                $product->subproducts->each(function ($product) {
                    $product['amount'] = $product['pivot']['amount'];
                    $product['default_amount'] = $product['amount'];
                    unset($product['pivot']);
                    $product['categories']->map(function ($category) {
                        unset($category['pivot']);
                        return $category;
                    });
                });
                return $product;
            });
        } else {

            $data = $data->with(['brand:id,name', 'measure:id,symbol'])->withCount('categories')->withCount('subproducts');
            //append shorters to query
            foreach ($sorters as $shorter) {
                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
        }
        return $data;
    }
}
