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
use App\Http\Traits\DynamicUpdater;

class UpdateResource implements CRUD
{
    use DynamicUpdater;

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
            $this->dynamicUpdate($request, $third, $userId);

            $warehouse->fill([
                'note' => $request->input('note') ?? null,
                'city_id' => $request->input('city_warehouse_id'),
                'status' => $request->input('status'),
                'users_update_id' => $userId,
                'address' => $request->input('address_warehouse'),
            ])->save();

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
