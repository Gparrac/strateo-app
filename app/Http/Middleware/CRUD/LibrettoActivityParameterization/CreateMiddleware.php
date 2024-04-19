<?php

namespace App\Http\Middleware\CRUD\LibrettoActivityParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'name' => 'required|string',
            'description' => 'string',
            'status' => 'required|in:A,I',
            'products_ids' => 'array',
            'file' => 'file|mimes:pdf,docx,jpg,jpeg,png|max:2048',
            'products_ids.*' => 'numeric|exists:products,id'
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        $names = $request->input('names');
        $surnames = $request->input('surnames');
        $business_name = $request->input('business_name');

        if($names && $surnames && $business_name){
            return ['error' => TRUE, 'message' => 'too much names fields for request'];
        }

        return ['error' => FALSE];
    }
}
