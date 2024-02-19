<?php

namespace App\Http\Controllers\CRUD\ProductParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Product;
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
            // Client update
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
                'supply'
                ])+ ['users_update_id' => $userId])->save();
                //record categories 🚨
                $product->categories()->get()->each(function($rProduct) use ($userId, $product){
                    $product->categories()->updateExistingPivot($rProduct,[
                        'status' => 'I',
                        'users_update_id' => $userId,
                    ]);
                });
                foreach ($request['categories_id'] as $value) {
                    $query = DB::table('categories_products')->where('product_id',$product['id'])->where('category_id',$value);
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
                $product->childrenProducts()
                ->get()->each(function($rProduct) use ($userId, $product){
                    $product->childrenProducts()->updateExistingPivot($rProduct,[
                        'status' => 'I',
                        'users_update_id' => $userId,
                    ]);
                });
                $product->taxes()->get()->each(function($rProduct) use ($userId, $product){
                    $product->taxes()->updateExistingPivot($rProduct,[
                        'status' => 'I',
                        'users_update_id' => $userId,
                    ]);
                });
                // filling out taxes
                if($request->has('taxes')){
                    foreach ($request['taxes'] as $value) {
                        $query = DB::table('products_taxes')->where('product_id',$product['id'])->where('tax_id',$value['tax_id']);
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

                if($request->has('products')){
                    foreach ($request['products'] as $value) {
                        $query = DB::table('products_products')->where('parent_product_id',$product['id'])->where('child_product_id',$value['product_id']);
                        if ($query->count() == 0) {
                            $product->childrenProducts()->attach($value['product_id'], [
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
}
