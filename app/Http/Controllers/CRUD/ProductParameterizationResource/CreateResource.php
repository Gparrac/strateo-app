<?php

namespace App\Http\Controllers\CRUD\ProductParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Planment;
use App\Models\Product;
use App\Models\ProductPlanment;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            if($request->has('type_connection')){
                if ($request['type_connection'] == 'F') {
                    $this->createFurtherEvent($userId, $request);
                }
                elseif ($request['type_connection'] == 'I') {
                    $this->createProductInvoice($userId, $request);
                }
                elseif ($request['type_connection'] == 'E') {
                    $this->createProductInvoice($userId, $request);
                }
            }else{
                $this->create($request, $userId);
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
    public function create(Request $request, $userId){
        $product = Product::create([
            'type' => $request->input('type'),
            'consecutive' => $request->input('consecutive'),
            'name' => $request->input('name'),
            'description' => $request->input('description') ?? null,
            'cost' => $request->input('cost'),
            'product_code' => $request->input('product_code') ?? null,
            'brand_id' => $request->input('brand_id'),
            'measure_id' => $request->input('measure_id'),
            'barcode' => $request->input('barcode') ?? null,
            'status' => $request->input('status'),
            'type_content' => $request->input('type_content') ?? null,
            'users_id' => $userId,
            'size' => $request['size'],
            'tracing' => $request->input('type') == 'T' ? $request['tracing'] : false
        ]);
        // filling out taxes
        if($request->has('taxes')){
            foreach ($request['taxes'] as $value) {
                $product->taxes()->attach($value['tax_id'], [
                    'users_id' => $userId,
                    'porcent' => $value['porcent'],
                    'status' => 'A'
                ]);
            }
        }

        foreach ($request['categories_id'] as $value) {
            $product->categories()->attach($value, [
                'users_id' => $userId,
                'status' => 'A'
            ]);
        }
        if( $request->has('type') == 'I'  and $request->has('products') ){
            foreach ($request['products'] as $value) {
                $product->childrenProducts()->attach($value['product_id'], [
                    'amount' => $value['amount'],
                    'users_id' => $userId,
                    'status' => 'A'
                ]);
        }
    }
    }
    protected function createFurtherEvent($userId, Request $request){
        $planment = Invoice::find($request['invoice_id'])->planment;
        foreach ($request['products'] as $product) {
            $planment->furtherProducts()->attach($product['product_id'], [
                'amount' => $product['amount'],
                'status' => 'A',
                'discount' => $product['discount'],
                'cost' => $product['cost'],
                'user_id' => $userId,
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
    protected function createProductInvoice($userId, Request $request){
        foreach ($request['products'] as $product) {
            Invoice::find('invoice_id')->products()->attach($product['product_id'], [
                'amount' => $product['amount'],
                'cost' => $product['cost'],
                'discount' => $product['discount'],
                'status' => 'A',
                'warehouse_id' => $product['warehouse_id'],
                'users_id' => $userId,
                'tracing' => $request['tracing']
            ]);
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
    protected function createProductEvent($userId, Request $request){
        $planment = Invoice::find($request['invoice_id'])->planment;
        foreach ($request['products'] as $event) {

            $productPlanment = ProductPlanment::create([
                'planment_id' => $planment['id'],
                'product_id' => $event['product_id'],
                'cost' => $request['cost'],
                'discount' => $event['discount'],
                'status' => 'A',
                'users_id' => $userId
            ]);
            foreach ($request['taxes'] as $tax) {
                DB::table('products_taxes')->insert([
                    'tax_id' => $tax['tax_id'],
                    'product_planment_id' => $productPlanment['id'],
                    'porcent' => $tax['porcent'],
                    'users_id' => $userId
                ]);
            }
            foreach ($event['sub_products'] as  $cProduct) {
                $productPlanment->subproducts()->attach($cProduct['product_id'], [
                    'product_activity_id' => $productPlanment['id'],
                    'product_id' => $cProduct['product_id'],
                    'amount' => $cProduct['amount'],
                    'user_id' => $userId,
                    'warehouse_id' => $cProduct['warehouse_id'] ?? null,
                    'tracing' => $cProduct['tracing']
                ]);
            }

        }
    }
}
