<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        return response()->json(['message' => 'Update'], 200);
    }
}
