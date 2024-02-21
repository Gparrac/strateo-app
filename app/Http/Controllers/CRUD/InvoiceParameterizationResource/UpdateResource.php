<?php

namespace App\Http\Controllers\CRUD\InvoiceParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\Third;
use App\Http\Utils\CastVerificationNit;
use App\Http\Traits\DynamicUpdater;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Planment;

class UpdateResource implements CRUD
{
    use DynamicUpdater;

    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            // -----------------------saving third ----------------------------
            $invoice = Invoice::findOrFail($request->input('invoice_id'));
            //Save the new files
            $invoice->fill($request->only([
                'client_id',
                'note',
                'seller_id',
                'further_discount',
            ]) + ['users_update_id' => $userId])->save();
            if ($request->input('state_type') == 'P') {
                $this->purchaseByProduct($request, $invoice, $userId);
            } else {
            }


            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error WarehouseResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error WarehouseResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
    protected function purchaseByProduct(Request $request, $invoice, $userId)
    {
        DB::table('products_invoices')->where('invoice_id', $invoice['id'])->update(['status' => 'I']);

        foreach ($request['products'] as $product) {
            $pinvoices = DB::table('products_invoices')->where('product_id', $product['product_id'])->where('invoice_id', $invoice['id']);
            if ($pinvoices->count() == 0) {
                $invoice->products()->attach($product['product_id'], [
                    'amount' => $product['amount'],
                    'cost' => $product['cost'],
                    'discount' => $product['discount'],
                    'status' => 'A',
                    'warehouse_id' => $product['warehouse_id'],
                    'users_id' => $userId
                ]);
            } else {
                $pinvoices->update([
                    'amount' => $product['amount'],
                    'cost' => $product['cost'],
                    'discount' => $product['discount'],
                    'status' => 'A',
                    'warehouse_id' => $product['warehouse_id'],
                    'users_id' => $userId,
                    'users_update_id' => $userId
                ]);
            }
            //updating product's invoices
            DB::table('products_taxes')->where('product_invoice_id', $pinvoices)->delete();
            foreach ($product['taxes'] as $tax) {
                DB::table('products_taxes')->insert([
                    'tax_id' => $tax['tax_id'],
                    'product_invoice_id' => $pinvoices,
                    'porcent' => $tax['porcent'],
                    'users_id' => $userId,
                ]);
            }
        }
    }
    protected function purchaseByEvent(Request $request, $invoice, $userId)
    {
        $planment = $invoice->planment;

        $planment->fill($request->only([
            'start_date',
            'end_date',
            'stage',
            'status',
            'pay_off'
        ]) + ['users_update_id' => $userId])->save();
        if (in_array($planment->stage, ['CON', 'REA', 'FIN'])) {
            //updating products' stock for returning
            $savedProducts = DB::table('products_planments')
                ->join('products_planments_products', 'products_planments_products.prodcut_activity_id', 'products_planments.id')
                ->where('planment_id', $planment['id'])->select('products_planments_products.id', 'products_planments_products.warehouse_id as product_id', 'products_planments_products.warehouse_id as warehouse_id', 'products_planments_products.amount ')->get();
            foreach ($savedProducts as $savedProduct) {
                $product = Inventory::where('product_id', $savedProduct['product_id'])->where('warehouse_id', $savedProduct['warehouse_id'])->first();
                $product->update([
                    'stock' => $savedProduct['amount'] + $product['stock'],
                    'users_update_id' => $userId
                ]);
                DB::table('products_planments_products')
                    ->where('id', $savedProduct['id'])->delete();

            }

        }
    }
}
