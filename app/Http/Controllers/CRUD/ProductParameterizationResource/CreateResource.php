<?php

namespace App\Http\Controllers\CRUD\ProductParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        Log::info('PASANDO');
        Log::info($request->supply);
        DB::beginTransaction();
        try {
            $userId = auth()->id();
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
                    'supply' => $request['supply']
                ]);
                // filling out taxes
                if($request->has('taxes')){
                    foreach ($request['taxes'] as $value) {
                        $product->taxes()->attach($value['tax_id'], [
                            'users_id' => $userId,
                            'cost' => $value['cost'],
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
