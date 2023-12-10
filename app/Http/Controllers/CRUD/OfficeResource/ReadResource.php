<?php

namespace App\Http\Controllers\CRUD\CompanyParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class ReadResource implements CRUD
{
    public function resource(Request $request)
    {
        try {

        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}