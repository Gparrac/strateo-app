<?php

namespace App\Http\Middleware\CRUD\TaxParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_id' => 'required|exists:taxes,id',
            'name' => 'required|string|min:3|max:45',
            'acronym' => 'required|string|min:1|max:5',
            'status' => 'required|in:A,I',
            'default_percent' => 'required|numeric|between:-1,1|regex:/^-?\d+(\.\d{1,3})?$/',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
