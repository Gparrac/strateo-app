<?php

namespace App\Http\Controllers\CRUD\InvoiceParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;
use App\Models\Third;
use Illuminate\Support\Facades\Auth;
use App\Http\Utils\CastVerificationNit;
use App\Models\Invoice;
use App\Models\Planment;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            // -----------------------saving invoice ----------------------------
            $invoice = Invoice::create([
                'client_id' => $request['client_id'],
                'note' => $request->has('note') ? $request->input('note') : null,
                'seller_id' => $request['seller_id'],
                'further_discount' => $request['further_discount'],
                'status' => 'A',
                'users_id' => $userId
            ]);
            //checking type of state
            if ($request->input('state_type') == 'P') {
                $this->purchaseByProduct($request, $invoice, $userId);
            } else {
                $this->purchaseByEvent($request, $invoice, $userId);
            }
            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
    protected function purchaseByProduct(Request $request, $invoice, $userId)
    {
        //saving products to invoice type products list
        foreach ($request['products'] as $product) {
            $invoice->products()->attach($product['product_id'], [
                'amount' => $product['amount'],
                'cost' => $product['cost'],
                'discount' => $product['discount'],
                'status' => 'A',
                'warehouse_id' => $product['warehouse_id'],
                'users_id' => $userId
            ]);
            $pivotId = DB::table('products_invoices')->where('product_id', $product['product_id'])->where('invoice_id', $invoice['id'])->first()->id;
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
    protected function purchaseByEvent(Request $request, $invoice, $userId)
    {
        //saving products to invoice type events list
        $planment = Planment::create([
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'stage' => $request['pay_off'] > 0 ? 'CON' : 'REA',
            'status' => $request['status'],
            'pay_off' => $request['pay_off'],
            'invoice_id' => $invoice['id'],
            'users_id' => $userId
        ]);


    }
}
