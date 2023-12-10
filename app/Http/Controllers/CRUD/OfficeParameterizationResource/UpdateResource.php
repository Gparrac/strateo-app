<?php

namespace App\Http\Controllers\CRUD\OfficeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Models\Office;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = auth()->id() ?? 1;

            $office = Office::findOrFail($request->input('office_id'));
            // Create a record in the Office table
            $office->fill($request->only([
                'name',
                'address',
                'phone',
                'city_id',
                'status',
            ]) + ['users_update_id' => $userId])->save();

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error OfficeResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error OfficeResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}