<?php

namespace App\Http\Controllers\CRUD\TaxParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tax;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = Auth::id();

            $tax = Tax::create([
                'name' => $request->input('name'),
                'acronym' => $request->input('acronym'),
                'status' => $request->input('status'),
                'type' => $request->input('type'),
                'users_id' => $userId,
                'context' => $request->input('context')
            ]);
            foreach ($request->input('values') as $value) {
                $tax->taxValues()->attach($value['tax_value_id']);
            }
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error TaxResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error TaxResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
