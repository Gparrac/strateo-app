<?php

namespace App\Http\Controllers\CRUD\CompanyParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Models\Office;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = auth()->id();

            // Create a record in the Office table
            $office = Office::create([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'city_id' => $request->input('city_id'),
                'status' => $request->input('status'),
                'users_id' => $userId,
            ]);

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error OfficeResource@createResource: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error OfficeResource@createResource: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}