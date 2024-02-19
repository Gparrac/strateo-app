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
                //saving products to invoice type products list
                DB::table('products_invoices')->where('invoice_id', $invoice['id'])->update(['status' => 'I']);
                if ($request->has('products')) {
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
            } else {
                $planment = $invoice->planment;
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
    public function validateState(Planment $planment, Request $request)
    {
        if ($planment->state == 'QUO') {
            if ($request['stage'] == 'CON') {
                //validate existency of pay off
            } else { //cancel
                //just change stage
            }
        } elseif ($planment->state == 'CON') {
            if ($request['stage'] == 'QUO') {
                //delete payment
            } elseif ($request['stage'] == 'REA') {
                // discount inventory and lock employees to set aside
            } else { //cancel
                //return  inventory and unlock employees  to change stage
            }
        } elseif ($request['stage'] == 'REA') {
            if ($request['stage'] == 'CON') {
                //return inventory and unlock employees schedule
            } elseif ($request['stage'] == 'FIN') {
                // return inventory and unlock employees
            } else { //cancel
                //return inventory and unlock employees schedule
            }
        } else {
            if ($request['stage'] == 'QUO') {
                //unlock inventory and employees schedule
            }
        }
    }
}
