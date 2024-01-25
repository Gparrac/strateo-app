<?php

namespace App\Http\Controllers\CRUD\MeasureParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Measure;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $measure = Measure::where('id', $request->input('measure_id'))->firstOrFail();

            $measure->fill($request->only([
                'type',
                'name',
                'symbol',
                'status',
            ]) + ['users_update_id' => $userId])->save();

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error MeasureResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error MeasureResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
