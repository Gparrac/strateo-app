<?php

namespace App\Http\Controllers\CRUD\WarehouseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $warehouse = Warehouse::where('id', $request->input('warehouse_id'))->firstOrFail();

            $warehouse->fill($request->only([
                'capacity',
                'third_id',
                'city_id',
                'status',
            ]) + ['users_update_id' => $userId])->save();

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
