<?php

namespace App\Http\Controllers\CRUD\ProductParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Planment;
use App\Models\Product;
use App\Models\ProductPlanment;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            if($request->has('type_connection')){
                if ($request['type_connection'] == 'F') {
                    $this->updateFurtherEvent($userId, $request);
                }
                elseif ($request['type_connection'] == 'I') {
                    $this->updateProductInvoice($userId, $request);
                }
                elseif ($request['type_connection'] == 'E') {
                    $this->updateProductPlanments($userId, $request);
                }
            }else{
                $this->update($request, $userId);
            }

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error ClientResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error ClientResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
    protected function update(Request $request, $userId)
    {
        $product = Product::findOrFail($request->input('product_id'));
        //Save the new files
        $product->fill($request->only([
            'type',
            'consecutive',
            'name',
            'description',
            'cost',
            'product_code',
            'brand_id',
            'measure_id',
            'barcode',
            'photo1',
            'photo2',
            'photo3',
            'status',
            'size',
            'type_content',
            'tracing'
        ]) + ['users_update_id' => $userId])->save();
        //record categories 🚨
        $product->categories()->get()->each(function ($rProduct) use ($userId, $product) {
            $product->categories()->updateExistingPivot($rProduct, [
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
        });
        foreach ($request['categories_id'] as $value) {
            $query = DB::table('categories_products')->where('product_id', $product['id'])->where('category_id', $value);
            if ($query->count() == 0) {
                $product->categories()->attach($value, [
                    'status' => 'A',
                    'users_id' => $userId,
                ]);
            } else {
                $query->update([
                    'status' => 'A',
                    'users_update_id' => $userId
                ]);
            }
        }
        //record children products 🚨
        $product->subproducts()
            ->get()->each(function ($rProduct) use ($userId, $product) {
                $product->subproducts()->updateExistingPivot($rProduct, [
                    'status' => 'I',
                    'users_update_id' => $userId,
                ]);
            });
        $product->taxes()->get()->each(function ($rProduct) use ($userId, $product) {
            $product->taxes()->updateExistingPivot($rProduct, [
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
        });
        // filling out taxes
        if ($request->has('taxes')) {
            foreach ($request['taxes'] as $value) {
                $query = DB::table('products_taxes')->where('product_id', $product['id'])->where('tax_id', $value['tax_id']);
                if ($query->count() == 0) {
                    $product->taxes()->attach($value['tax_id'], [
                        'status' => 'A',
                        'users_id' => $userId,
                        'porcent' => $value['porcent']
                    ]);
                } else {
                    $query->update([
                        'status' => 'A',
                        'porcent' => $value['porcent'],
                        'users_update_id' => $userId
                    ]);
                }
            }
        }

        if ($request->has('products')) {
            foreach ($request['products'] as $value) {
                $query = DB::table('products_products')->where('parent_product_id', $product['id'])->where('child_product_id', $value['product_id']);
                if ($query->count() == 0) {
                    $product->subproducts()->attach($value['product_id'], [
                        'status' => 'A',
                        'users_id' => $userId,
                        'amount' => $value['amount']
                    ]);
                } else {
                    $query->update([
                        'status' => 'A',
                        'amount' => $value['amount'],
                        'users_update_id' => $userId
                    ]);
                }
            }
        }
    }
    protected function updateFurtherEvent($userId, Request $request){
        $planment = Invoice::find($request['invoice_id'])->planment;
        $currentlyProducts = $planment->furtherProducts()->pluck('id')->toArray();
        $planment->furtherProducts()->detach($currentlyProducts);
        foreach ($request['products'] as $product) {
            $planment->furtherProducts()->attach($product['product_id'], [
                'amount' => $product['amount'],
                'status' => 'A',
                'discount' => $product['discount'],
                'cost' => $product['cost'],
                'users_id' => $userId,
                'warehouse_id' => $product['warehouse_id'] ?? null,
                'tracing' =>  $product['tracing'],
            ]);
            foreach ($product['taxes'] as $tax) {
                DB::table('products_taxes')->insert([
                    'tax_id' => $tax['tax_id'],
                    'further_product_planment_id' => $planment['id'],
                    'porcent' => $tax['porcent'],
                    'users_id' => $userId
                ]);
            }
        }

    }
    protected function updateProductInvoice($userId, Request $request)
    {
        Log::info($request);
        Log::info('t1');
        $currentlyProducts = Invoice::find($request['invoice_id'])->products();
        if(empty($currentlyProducts)){
            $currentlyProducts = $currentlyProducts->pluck('id')->toArray();
            Invoice::find($request['invoice_id'])->products()->detach($currentlyProducts);
        }
        Log::info('t2');
        Log::info('t3');
        foreach ($request['products'] as $product) {
            Invoice::find($request['invoice_id'])->products()->attach($product['product_id'], [
                'amount' => $product['amount'],
                'cost' => $product['cost'],
                'discount' => $product['discount'],
                'status' => 'A',
                'warehouse_id' => $product['warehouse_id'],
                'users_id' => $userId,
                'tracing' => $product['tracing']
            ]);
            Log::info('t4');
            $pivotId = DB::table('products_invoices')->where('product_id', $product['product_id'])->where('invoice_id', $request['invoice_id'])->first()->id;
            foreach ($product['taxes'] as $tax) {
                DB::table('products_taxes')->insert([
                    'tax_id' => $tax['tax_id'],
                    'product_invoice_id' => $pivotId,
                    'porcent' => $tax['porcent'],
                    'users_id' => $userId,
                ]);
            }
        }

    }
    protected function updateProductPlanments($userId, Request $request)
    {

        $planment = Invoice::find($request['invoice_id'])->planment;
            $checkState = in_array($planment->stage, ['REA', 'FIN']);
            ProductPlanment::where('planment_id', $planment['id'])->delete();
        foreach ($request['products'] as $event) {
            $productPlanment = ProductPlanment::create([
                'planment_id' => $planment['id'],
                'product_id' => $event['product_id'],
                'cost' => $event['cost'],
                'discount' => $event['discount'],
                'users_id' => $userId
            ]);
            foreach ($event['taxes'] as $tax) {
                DB::table('products_taxes')->insert([
                    'tax_id' => $tax['tax_id'],
                    'product_planment_id' => $productPlanment['id'],
                    'porcent' => $tax['porcent'],
                    'users_id' => $userId
                ]);
            }
            foreach ($event['sub_products'] as  $subproduct) {

                ProductPlanment::find($productPlanment['id'])->subproducts()->attach($subproduct['product_id'], [
                    'product_activity_id' => $productPlanment['id'],
                    'product_id' => $subproduct['product_id'],
                    'amount' => $subproduct['amount'],
                    'users_id' => $userId,
                    'warehouse_id' => $subproduct['warehouse_id'] ?? null,
                    'tracing' => $subproduct['tracing']
                ]);
                if ($checkState && $subproduct['tracing']) {
                    Log::info('Orden de compra creada para abastecimiento de planeación ' . $planment->id);
                }
            }
        }
    }
}
