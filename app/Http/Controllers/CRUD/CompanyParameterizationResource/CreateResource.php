<?php

namespace App\Http\Controllers\CRUD\CompanyParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        return response()->json(['message' => 'Create'], 200);
    }
}