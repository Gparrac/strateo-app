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

// -----------------------saving third ----------------------------
            // $thirdData = [
            //     'type_document' => $request->input('type_document'),
            //     'identification' => $request->input('identification'),
            //     'verification_id' => $request['tupe_document'] == 'NIT' ? CastVerificationNit::calculate($request['identification']) : NULL ,
            //     'names' => $request->input('names') ?? null,
            //     'surnames' => $request->input('surnames') ?? null,
            //     'business_name' => $request->input('business_name') ?? null,
            //     'address' => $request->input('address'),
            //     'mobile' => $request->input('mobile'),
            //     'email' => $request->input('email'),
            //     'email2' => $request->input('email2'),
            //     'postal_code' => $request->input('postal_code'),
            //     'city_id' => $request->input('city_id'),
            //     'users_id' => $userId,
            //     'code_ciiu_id' => $request->input('code_ciiu_id')
            // ];
            // Check if 'email2' is present in the request before adding it to the array
            // if ($request->has('email2')) {
            //     $thirdData['email2'] = $request->input('email2');
            // }
            // // create or update ciiu record
            // $checkCiiu = $request->has('secondary_ciiu_ids');
            // if( $request->has('third_id')){
            //     Third::find($request['third_id'])->update($thirdData);
            //     $third = Third::find($request['third_id']);
            //     if(DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->count() > 0){
            //         DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->update([
            //             'status' => 'I',
            //             'users_update_id' => $userId
            //         ]);
            //     }
            //     if($checkCiiu){
            //         foreach ($request['secondary_ciiu_ids'] as $value) {
            //             $codes = DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->where('code_ciiu_id', $value);
            //             if($codes->count() == 0){
            //                 $third->secondaryCiius()->attach($value,[
            //                     'status' => 'A',
            //                     'users_id' => $userId,
            //                     'users_update_id' => $userId
            //                 ]);
            //             }else{
            //                 $codes->update([
            //                     'status' => 'A'
            //                 ]);
            //             }
            //         }
            //     }
            // }else{
            //     $third = Third::create($thirdData);
            //     if($checkCiiu){
            //         $third->secondaryCiius()->attach($request['secondary_ciiu_ids'],[
            //             'status' => 'A',
            //             'users_id' => $userId
            //         ]);
            //     }
            // }

            // if($request->has('secondary_ciiu_ids')){
            //     $third->secondaryCiius()->attach($request['secondary_ciiu_ids'],[
            //         'status' => 'A',
            //         'users_id' => $userId,
            //         'users_update_id' => $userId,
            //     ]);
            // }
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
            if($request->input('state_type') == 'P'){
                //saving products to invoice type products list
                foreach ($request['products'] as $product) {
                    $invoice->products()->attach($product['product_id'],[
                        'amount' => $product['amount'],
                        'cost' => $product['cost'],
                        'discount' => $product['discount'],
                        'status' => 'A',
                        'warehouse_id' => $product['warehouse_id'],
                        'users_id' => $userId
                    ]);
                    $pivotId = DB::table('products_invoices')->where('product_id', $product['product_id'])->where('invoice_id',$invoice['id'])->first()->id;
                    foreach ($product['taxes'] as $tax) {
                        DB::table('products_taxes')->insert([
                            'tax_id' => $tax['tax_id'],
                            'product_invoice_id' => $pivotId,
                            'porcent' => $tax['porcent'],
                            'users_id' => $userId,
                        ]);
                    }
                }
            }else{
                //saving products to invoice type events list
                $planment = Planment::create([
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                    'stage' => $request['stage'],
                    'status'=> $request['status'],
                    'pay_off' => $request['pay_off'],
                    'invoice_id' => $invoice['id'],
                    'users_id' => $userId
                ]);
                // relating products with planment
                foreach ($request['products'] as $event) {
                    $totalCostEvent = 0;
                    $ppId = DB::table('products_planments')->insertGetId([
                        'planment_id' => $planment['id'],
                        'product_id' => $event['id'],
                        'cost' => $totalCostEvent,
                        'discount' => $event['discount'],
                        'status' => 'A',
                        'users_id' => $userId
                    ]);
                    foreach ($request['taxes'] as $tax) {
                        DB::table('products_taxes')->insert([
                            'tax_id' => $tax['tax_id'],
                            'product_planment_id' => $planment['id'],
                            'porcent' => $tax['porcent'],
                            'users_id' => $userId
                        ]);
                    }
                    foreach ($event['children_products'] as  $cProduct) {
                        DB::table('products_planments_products')->insert([
                            'product_activity_id' => $ppId,
                            'product_id' => $cProduct['id'],
                            'cost' => $cProduct['cost'],
                            'amount' => $cProduct['amount'],
                            'user_id' => $userId,
                            'warehouse_id' => $cProduct['warehouse_id'] ?? null,
                        ]);
                        $totalCostEvent = $totalCostEvent + ($cProduct['cost'] * $cProduct['amount']);
                    }
                    DB::table('products_planments')->where('id', $ppId)->update([
                        'cost' => $totalCostEvent
                    ]);
                }
                foreach ($$request['further_products'] as $value) {
                    $planment->furtherProducts()->attach($value['product_id'], [
                        'amount' => $planment['amount'],
                        'status' => 'A',
                        'discount' => $planment['discount'],
                        'cost' => $planment['cost'],
                        'user_id' => $userId,
                        'warehouse_id' => $value['warehouse_id'] ?? null,
                    ]);
                    foreach ($value['taxes'] as $tax) {
                        DB::table('products_taxes')->insert([
                            'tax_id' => $tax['tax_id'],
                            'further_product_planment_id' => $planment['id'],
                            'porcent' => $tax['porcent'],
                            'users_id' => $userId
                        ]);
                    }
                }
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
}
