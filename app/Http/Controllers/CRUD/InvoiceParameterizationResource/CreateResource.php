<?php

namespace App\Http\Controllers\CRUD\InvoiceParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\Planment;

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
                'status' => 'A',
                'users_id' => $userId,
                'sale_type' => $request['sale_type'],
                'date' => $request['date']
            ]);
            if ($request->has('taxes'))
            foreach ($request['taxes'] as  $value) {
                $invoice->taxes()->attach($value['tax_id'], [
                    'percent' => $value['percent'],
                    'status' => 'A',
                    'users_id' => $userId,
                ]);
            }
            if($request->sale_type == 'E') {
                Planment::create([
                   'start_date' => $request['start_date'],
                   'end_date' => $request['end_date'],
                   'pay_off' => $request['pay_off'] ?? null,
                   'users_id' => $userId,
                   'stage' => 'QUO',
                   'status' => 'A',
                   'invoice_id' => $invoice['id']
                ]);
            }
            //checking type of state
            DB::commit();
            return response()->json(['message' => 'Successful', 'data' => $invoice->id]);
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

}
