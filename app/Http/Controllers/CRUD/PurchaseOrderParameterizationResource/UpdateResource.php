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

            // Synchronize the attached products with the new product IDs
            foreach ($products as $product) {
                $purchaseOrder->products()->sync([$product['product_id'] => ['amount' => $product['amount'], 'users_update_id' => $userId]], false);
            }

            // Get the IDs of the attached products after synchronization
            $attachedProductIds = $purchaseOrder->products()->pluck('products.id')->toArray();
            $productIds = $products->pluck('id')->toArray();

            // Determine the product IDs to remove
            $productsToDetach = array_diff($attachedProductIds, $productIds);

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
