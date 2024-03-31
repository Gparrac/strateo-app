<?php

namespace App\Http\Middleware\CRUD\TaxParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:45',
            'acronym' => 'required|string|min:1|max:5',
            'status' => 'required|in:A,I',
            'type' => 'required|in:I,D',
            'default_percent' => 'required|numeric|between:-99,99|regex:/^-?\d+(\.\d{2,3})?$/',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
