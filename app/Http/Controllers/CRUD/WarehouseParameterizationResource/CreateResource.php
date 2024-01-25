<?php

namespace App\Http\Controllers\CRUD\WarehouseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = Auth::id();
            
            Warehouse::create([
                'capacity' => $request->input('capacity'),
                'third_id' => $request->input('third_id'),
                'city_id' => $request->input('city_id'),
                'status' => $request->input('status'),
                'users_id' => $userId,
            ]);

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
