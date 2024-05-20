<?php

namespace App\Http\Controllers\CRUD\ChargeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\DynamicUpdater;
use App\Models\Charge;
use App\Models\EmployeePlanment;
use App\Models\PaymentMethod;

class CreateResource implements CRUD
{
    use DynamicUpdater;

    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            Charge::create([
                'name' => $request['name'],
                'description' => $request['description'],
                'status' => $request['status'],
                'users_id' => $userId
            ]);

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
