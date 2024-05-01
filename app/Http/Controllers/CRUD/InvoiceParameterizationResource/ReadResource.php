<?php

namespace App\Http\Controllers\CRUD\InvoiceParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Http\Utils\FileFormat;
use App\Models\EmployeePlanment;
use App\Models\FurtherProductPlanment;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\LibrettoActivity;
use App\Models\Planment;
use App\Models\Product;
use App\Models\ProductInvoice;
use App\Models\ProductPlanment;
use App\Models\ProductPlanmentProduct;
use App\Models\SubproductPlanment;
use App\Models\Warehouse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;


class ReadResource implements CRUD, RecordOperations
{
    protected $typeSale;
    public function resource(Request $request)
    {
        try {
            if ($request->has('invoice_id')) {
                if ($request->has('attribute_key')) {
                    switch ($request->input('attribute_key')) {
                        case 'F':
                            $data = $this->getFurtherProducts($request->input('invoice_id'));
                            break;
                        case 'I':;
                            $data = $this->getInvoiceProducts($request->input('invoice_id'));
                            break;
                        case 'E': // E
                            $data = $this->getEventProducts($request->input('invoice_id'));
                            break;
                        case 'L': // E
                            $data = $this->getLibrettoActivies($request->input('invoice_id'), $request->input('type_service'));
                            break;
                        case 'S':
                            $data = $this->getSubproducts($request->input('invoice_id'));
                            break;
                        default:
                            $data = $this->getEmployees($request->input('invoice_id'));
                    }
                } else {
                    $data = $this->singleRecord($request->input('invoice_id'));
                }
            } else {
                $this->typeSale = $request->input('type');
                $data = $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [] ,$request->input('keyword') ?? [], $request->input('format'));
            }
            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error InvoiceResource@readResource:getInvoiceProduct: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error InvoiceResource@readResource:getInvoiceProduct: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function singleRecord($id)
    {
        $data = Invoice::where('id', $id)
            ->with(['planment:id,start_date,end_date,pay_off,invoice_id,stage', 'taxes' => function ($query) {
                $query->with('taxValues:id,percent');
                $query->select('taxes.id', 'name', 'acronym', 'type');
            }, 'seller' => function ($query) {
                $query->with('third:id,names,surnames,identification,type_document');
                $query->select('users.id', 'users.third_id', 'users.name');
            }, 'client' => function ($query) {
                $query->select('clients.id', 'legal_representative_name as name', 'legal_representative_id as document');
            }]);

        $data = $data->first();
        $data['taxes']->each(function ($item) {
            $item['percent'] = $item['pivot']['percent'];
            unset($item['pivot']);
        });
        $test = [
            'id' => $data->seller->id,
            'full_name' => $data->seller->third->names . ' ' . $data->seller->third->surnames,
            'identification' => $data->seller->third->type_document . ' ' . $data->seller->third->identification,
            'name' => $data->name
        ];
        unset($data->seller);
        $data['seller'] = $test;
        return $data;
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        $data = Invoice::with(['seller:id,name', 'client' => function ($query) {
            $query->with('third:id,identification,names,surnames,type_document')->select('id', 'third_id');
        }]);
        if ($this->typeSale == 'E') {
            $data = $data->whereHas('planment')->with('planment:id,invoice_id,stage,pay_off,start_date,end_date');
        }
        if ($filters) {
            foreach ($filters as  $value) {
                if (in_array($value['key'], ['client', 'client_id'])) {
                    $data = $data->whereHas('client', function ($query) use ($value) {
                        $query->whereHas('third', function ($query) use ($value) {
                            ($value['key'] == 'client') ?
                                $query->whereRaw("UPPER(CONCAT(names, ' ', surnames)) LIKE ?", ['%' . strtoupper($value['value']) . '%'])
                                :
                                $query->whereRaw('LOWER(identification) LIKE ?', ['%' . strtolower($value['value']) . '%']);
                        });
                    });
                }
                if ($value['key'] == 'id') {
                    $data = $data->where('id', 'like', '%' . $value['value'] . '%');
                }
                if ($value['key'] == 'seller') {
                    $data = $data->whereHas('seller', function ($query) use ($value) {
                        $query->where('name', 'like', '%' . $value['value'] . '%');
                    });
                }
                if ($value['key'] == 'stages' && $this->typeSale = 'E') {
                    $data = $data->whereHas('planment', function ($query) use ($value) {
                        $query->whereIn('stage', $value['value']);
                    });
                }
            }
        }
        if ($format == 'short') {
            $data = $data->where('status', 'A')->select('warehouses.id', 'warehouses.address', 'warehouses.city_id')->take(10)->get();
        } else {
            if ($this->typeSale)
                $data = $data->where('sale_type', $this->typeSale);
            //append shorters to query
            foreach ($sorters as $shorter) {
                if ($shorter['key'] == 'stage') $shorter['key'] = 'planment.stage';
                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
        }
        return $data;
    }
    protected function getFurtherProducts($invoice)
    {
        $planment = Invoice::find($invoice)->planment;
        if ($planment) {
            $products = FurtherProductPlanment::with(['product' => function ($query) {
                $query->with(['measure:id,symbol', 'brand:id,name']);
                $query->select('products.id', 'products.name', 'products.consecutive', 'products.product_code', 'products.brand_id', 'products.measure_id', 'products.cost as defaultCost');
            }, 'warehouse' => function ($query) {
                $query->with('city:id,name')->select('id', 'city_id', 'address');
            }, 'taxes' => function ($query) {
                $query->with('taxValues:id,percent');
                $query->select('taxes.id', 'name', 'acronym', 'type');
            }])->where('planment_id', $planment->id)
                //->select('further_products_planments.id as further_product_planment_id', 'further_products_planments.planment_id', 'further_products_planments.product_id', 'further_products_planments.tracing', 'further_products_planments.warehouse_id', 'further_products_planments.amount', 'further_products_planments.cost', 'further_products_planments.discount')
                ->get();
            $products->each(function ($product, $key) use ($products) {
                $inventory = ($product['warehouse']) ? Inventory::where('product_id', $product['id'])->where('warehouse_id', $product['warehouse']['id'])->first() : 0;
                $temp = $product['product']->toArray() + [
                    'stock' => $inventory['stock'] ?? 0,
                    'warehouse' => $product['warehouse'],
                    'amount' => $product['amount'],
                    'cost' => $product['cost'],
                    'discount' => $product['discount'],
                    'tracing' => $product['tracing'] ?? 0
                ];
                $product['taxes']->map(function ($tax) {
                    $tax['percent'] = $tax['pivot']['percent'];;
                    unset($tax['pivot']);
                });
                $temp['taxes'] = $product['taxes'];
                $products[$key] = $temp;
            });
        } else {
            $products = [];
        }
        return $products;
    }
    protected function getInvoiceProducts($invoice)
    {

        $products = ProductInvoice::with([
            'product' => function ($query) {
                $query->with(['measure:id,symbol', 'brand:id,name']);
                $query->select('products.id', 'products.name', 'products.consecutive', 'products.product_code', 'products.brand_id', 'products.measure_id', 'products.cost as defaultCost');
            }, 'warehouse' => function ($query) {
                $query->with('city:id,name')->select('id', 'city_id', 'address');
            },
            'taxes' => function ($query) {
                $query->with('taxValues:id,percent');
                $query->select('taxes.id', 'name', 'acronym', 'type');
            }
        ])->where('invoice_id', $invoice)
            ->select('products_invoices.id', 'products_invoices.invoice_id', 'products_invoices.product_id', 'products_invoices.tracing', 'products_invoices.warehouse_id', 'products_invoices.amount', 'products_invoices.cost', 'products_invoices.discount')
            ->get();

        $products->each(function ($product, $key) use ($products) {
            $inventory = $product['warehouse'] ? Inventory::where('product_id', $product['id'])->where('warehouse_id', $product['warehouse']['id'])->first() : null;
            $temp = $product['product']->toArray() + [
                'stock' => $inventory['stock'] ?? 0,
                'warehouse' => $product['warehouse'],
                'amount' => $product['amount'],
                'cost' => $product['cost'],
                'discount' => $product['discount'],
                'tracing' => $product['tracing']
            ];
            $product['taxes']->map(function ($tax) {
                $tax['percent'] = $tax['pivot']['percent'];
                unset($tax['pivot']);
            });
            $temp['taxes'] = $product['taxes'];
            $products[$key] = $temp;
        });
        return $products;
    }
    protected function getEventProducts($invoice)
    {
        $planmentId = Invoice::find($invoice)->planment->id;
        $products = ProductPlanment::with(['product' => function ($query) {
            $query->with(['measure:id,symbol', 'brand:id,name']);
            $query->select('products.id', 'products.size', 'products.name', 'products.consecutive', 'products.product_code', 'products.brand_id', 'products.measure_id', 'products.cost as defaultCost');
        }, 'subproductPlanments:id,product_id,tracing,warehouse_id', 'taxes' => function ($query) {
            $query->with('taxValues:id,percent');
            $query->select('taxes.id', 'name', 'acronym', 'type');
        }])->where('planment_id', $planmentId)->get();
        $products->each(function ($product, $key) use ($products) {
            $product->subproductPlanments->each(function ($spp, $skey) use ($product) {
                $event = Product::whereHas('subproductPlanments', function ($query) use ($spp) {
                    $query->where('subproducts_planments.product_id', $spp['product_id']);
                })->select('id', 'name')->first();
                $product['subproductPlanments'][$skey] = $event->toArray() + [
                    'amount' => $spp['pivot']['amount'],
                    'warehouse_id' => $spp['warehouse_id'],
                    'tracing' => $spp['tracing'],
                ];
            });
            $product->taxes->each(function ($tax) {
                $tax['id'] = $tax['pivot']['tax_id'];
                $tax['percent'] = $tax['pivot']['percent'];
                unset($tax['pivot']);
            });
            $temp = $product['product']->toArray() + [
                'cost' => $product['cost'],
                'amount' => $product['amount'],
                'discount' => $product['discount'],
                'taxes' => $product['taxes'],
                'temp' => $product['subproductPlanments']
            ];
            $products[$key] = $temp;
        });
        return $products;
    }
    protected function getEmployees($invoice)
    {
        $planmentId = Invoice::find($invoice)->planment->id;
        $eps = EmployeePlanment::with(['employee' =>  function ($query) {
            $query->with('third:id,names,surnames,business_name,identification,type_document', 'paymentMethods:id,name,description');
            $query->select('employees.id', 'third_id');
        },'paymentMethod:id,name,description','charges:id,name,description'])->where('planment_id', $planmentId)->select('employees_planments.id', 'employees_planments.planment_id', 'employees_planments.employee_id', 'employees_planments.salary', 'employees_planments.payment_method_id', 'employees_planments.reference')->get();
        // dd($eps);

        $eps = $eps->map(function ($p, $key) use ($eps) {
            $p['employee']['PaymentMethods']->each(function($pm, $i) use (&$p){
                $p['employee']['paymentMethods'][$i]['reference']= $pm['pivot']['reference'];
                unset($p['employee']['paymentMethods'][$i]['pivot']);
                      });
            return  [
                'id' => $p['employee']['id'],
                'fullname' => $p['employee']['third']['fullname'],
                'identification' => $p['employee']['third']['type_document'] . ':' . $p['employee']['third']['identification'],
                'salary' => $p['salary'],
                'payment_method_id' => $p['payment_method_id'],
                'default_payment_methods' => $p['employee']['paymentMethods'],
                'charges' => $p['charges'],
                'reference' => $p['reference']
            ];
        });
        return $eps;
    }
    protected function getLibrettoActivies($invoice, $typeService)
    {
        if ($typeService)
            return LibrettoActivity::join('libretto_activities_products', 'libretto_activities.id', 'libretto_activities_products.libretto_activity_id')
                ->join('products', 'libretto_activities_products.product_id', 'products.id')
                ->join('products_planments', 'products.id', 'products_planments.product_id')
                ->join('planments', 'products_planments.planment_id', 'planments.id')
                ->where('planments.invoice_id', $invoice)
                ->select('libretto_activities.id', 'libretto_activities.name', 'libretto_activities.description', 'libretto_activities.path_file as pathFile', 'products.name as service')->get();
        return LibrettoActivity::join('libretto_activities_planments', 'libretto_activities.id', 'libretto_activities_planments.libretto_activity_id')
            ->join('planments', 'planments.id', 'libretto_activities_planments.planment_id')
            ->where('planments.invoice_id', $invoice)
            ->select('libretto_activities.id', 'libretto_activities.name', 'libretto_activities_planments.description', 'libretto_activities_planments.path_file as pathFile')
            ->get();
    }
    protected function getSubproducts($invoice)
    {
        $planmentId = Invoice::find($invoice)->planment->id;
        $products = SubproductPlanment::with(['product' => function ($query) {
            $query->with(['measure:id,symbol', 'brand:id,name']);
            $query->select('products.id', 'products.size', 'products.name', 'products.consecutive', 'products.product_code', 'products.brand_id', 'products.measure_id', 'products.cost as defaultCost');
        }, 'productPlanments', 'warehouse' => function ($query) {
            $query->with('city:id,name')->select('id', 'city_id', 'address');
        }])->where('planment_id', $planmentId)->get();
        $products->each(function ($product, $key) use ($products) {
            $events = Product::join('products_planments', 'products.id', 'products_planments.product_id')
                ->join('product_planments_subproduct_planments', 'product_planments_subproduct_planments.product_planment_id', 'products_planments.id')
                ->where('product_planments_subproduct_planments.subproduct_planment_id', $product->id)
                ->select('products.id', 'products.name', 'product_planments_subproduct_planments.amount')->get();
            $temp = $product['product']->toArray() + [
                'events' => $events
            ];
            $products[$key] = $temp;
        });
        return $products;
    }
}
