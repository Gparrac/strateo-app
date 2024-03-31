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
            Log::info('test stage');
            Log::info($request->stage);
            $userId = Auth::id();
            // -----------------------saving third ----------------------------
            $invoice = Invoice::findOrFail($request->input('invoice_id'));
            //Save the new files
            $invoice->fill($request->only([
                'client_id',
                'note',
                'seller_id',
                'date',
                'sale_type',
                'further_discount',
            ]) + ['users_update_id' => $userId])->save();

            if ($invoice->sale_type['id'] == 'E') {
                Log::info('----s');
                Log::info($invoice->planment);
                if($invoice->planment){
                $invoice->planment->fill($request->only([
                    'start_date',
                    'end_date',
                    'stage',
                    'status',
                    'pay_off'
                ]) + ['users_update_id' => $userId])->save();
                }else{
                    Planment::create([
                        'start_date' => $request['start_date'],
                        'end_date' => $request['end_date'],
                        'pay_off' => $request['pay_off'],
                        'users_id' => $userId,
                        'stage' => 'QUO',
                        'status' => 'A',
                        'invoice_id' => $invoice['id']
                     ]);
                }
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

}
