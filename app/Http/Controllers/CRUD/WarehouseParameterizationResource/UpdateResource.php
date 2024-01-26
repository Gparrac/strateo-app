<?php

namespace App\Http\Controllers\CRUD\WarehouseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\Third;
use App\Http\Utils\CastVerificationNit;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $warehouse = Warehouse::where('id', $request->input('warehouse_id'))->firstOrFail();

            $third = Third::findOrFail($warehouse->third_id);
            // Create a record in the Third table
            $verificationId = CastVerificationNit::calculate($request['identification']);
            $third->fill($request->only([
                'type_document',
                'identificacion',
                'names',
                'surnames',
                'business_name',
                'address',
                'mobile',
                'email',
                'email2',
                'postal_code',
                'city_id',
                'code_ciiu_id',
            ]) + ['users_update_id' => $userId, 'verification_id' => $verificationId])->save();

            //secondary ciiu ids
            DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->update(['status' => 'I']);
            if($request->has('secondary_ciiu_ids')){
                foreach ($request['secondary_ciiu_ids'] as $key => $value) {
                    $codes = DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->where('code_ciiu_id', $value);
                    if($codes->count() == 0){
                        $third->secondaryCiius()->attach($value,[
                            'status' => 'A',
                            'users_id' => $userId,
                            'users_update_id' => $userId
                        ]);
                    }else{
                        $codes->update([
                            'status' => 'A'
                        ]);
                    }
                }
            }

            $warehouse->fill([
                'note' => $request->input('note'),
                'city_id' => $request->input('city_warehouse_id'),
                'status' => $request->input('status'),
                'users_update_id' => $userId,
            ])->save();

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
