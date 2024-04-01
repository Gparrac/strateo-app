<?php

namespace App\Http\Controllers\CRUD\PurchaseOrderParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $purchaseOrder = PurchaseOrder::where('id', $request->input('purchase_order_id'))->firstOrFail();

            $purchaseOrder->fill($request->only([
                'supplier_id',
                'date',
                'note',
                'status'
            ]) + ['users_update_id' => $userId])->save();

            // Attach products to the purchase order
            $products = $request->input('products');

            $attachedProductIds = $purchaseOrder->products();
            $attachedProductIds = count($products) > 0 ? $attachedProductIds->pluck('products.id')->toArray() : [];
            $productIds = count($products) > 0 ? array_column($products,'product_id') : [];

            $productsToDetach = array_diff($attachedProductIds, $productIds);
            DB::table('purchase_orders_products')
            ->where('purchase_order_id')
            ->whereIn('product_id',$productsToDetach)
            ->update(['status'=> 'I', 'users_update_id' => $userId]);
            foreach ($products as  $value) {
                $query = DB::table('purchase_orders_products')
                ->where('purchase_order_id', $purchaseOrder['id'])
                ->where('product_id',$value['product_id']);
                if($query->count() > 0){
                    $query->update([
                        'amount' => $value['amount'],
                        'users_update_id' => $userId,
                        'status' => 'A'
                    ]);
                }else{
                    $purchaseOrder->products()->attach($value['product_id'],[
                        'amount' => $value['amount'],
                        'users_id' => $userId,
                        'status' => 'A'
                    ]);
                }
            }





            // Determine the product IDs to remove

            // Delete products that are no longer present in the request
            $purchaseOrder->products()->detach($productsToDetach);

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error PurchaseOrder@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error PurchaseOrder@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
