<?php

namespace App\Http\Middleware\CRUD\FieldParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //--------------------- new attributes
            'name' => 'required|string',
            'type' =>'required|in:F,T,A,I',
            'length' => 'integer',
            'status' =>'required|in:A,I',
            //--------------------- others
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
