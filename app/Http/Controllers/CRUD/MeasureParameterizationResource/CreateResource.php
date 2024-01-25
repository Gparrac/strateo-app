<?php

namespace App\Http\Controllers\CRUD\MeasureParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Measure;
use Illuminate\Support\Facades\Auth;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = Auth::id();
            
            Measure::create([
                'type' => $request->input('type'),
                'name' => $request->input('name'),
                'symbol' => $request->input('symbol'),
                'status' => $request->input('status'),
                'users_id' => $userId,
            ]);

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error MeasureResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error MeasureResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
