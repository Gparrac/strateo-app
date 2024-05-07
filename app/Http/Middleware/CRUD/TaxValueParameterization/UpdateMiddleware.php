<?php

namespace App\Http\Middleware\CRUD\TaxValueParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_value_id' => 'required|exists:tax_values,id',
            'percent' => 'required|numeric|between:-99,99|regex:/^-?\d+(\.\d{2,3})?$/|unique:tax_values,percent',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
