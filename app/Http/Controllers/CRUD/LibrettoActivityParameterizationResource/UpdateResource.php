<?php

namespace App\Http\Controllers\CRUD\LibrettoActivityParameterizationResource;

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
use App\Models\LibrettoActivity;

class UpdateResource implements CRUD
{
    use DynamicUpdater;

    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            if($request->has('type_connection')){
                $this->connectInvoice($userId, $request);
            }else{
                $this->updateResource($userId, $request);
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
    protected function updateResource($userId, $request){
        $la = LibrettoActivity::findOrFail($request['libretto_activity_id']);
        $la->fill($request->only([
            'name',
            'description',
        ]) + ['users_update_id' => $userId])->save();
        $la->products()->get()->each(function($rCategory) use ($userId, $la){
            $la->products()->updateExistingPivot($rCategory,[
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
        });
        foreach ($request['product_ids'] ?? [] as $value) {
            $query = DB::table('libretto_activities_products')->where('libretto_activity_id', $la['id'])->where('product_id',$value);
            if ($query->count() == 0) {
                $la->products()->attach($value, [
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
    }
    protected function connectInvoice($userId, $request){

        $planment = Invoice::find($request['invoice_id'])->planment;
        $planment->librettoActivities()->detach();
        // $planment->librettoActivities()->get()->each(function($la) use ($userId, $planment){
        //     $planment->librettoActivities()->updateExistingPivot($la,[
        //         'status' => 'I',
        //         'users_update_id' => $userId,
        //     ]);
        // });

        foreach ($request['libretto_activities'] ?? [] as $value) {
            // coment section while searching a new method to update librettos without deleting information
            // $query = DB::table('libretto_activities_planments')->where('libretto_activity_id', $value['libretto_activity_id'])->where('planment_id',$planment['id']);
            // if ($query->count() == 0) {
                $planment->librettoActivities()->attach($value['libretto_activity_id'], [
                    'status' => 'A',
                    'users_id' => $userId,
                    'description' => $value['description'],
                    'order' => $value['order']
                ]);
            // } else {
            //     $query->update([
            //         'status' => 'A',
            //         'users_update_id' => $userId,
            //         'description' => $value['description'],
            //         'order' => $value['order']
            //     ]);
            // }
        }
    }
}
