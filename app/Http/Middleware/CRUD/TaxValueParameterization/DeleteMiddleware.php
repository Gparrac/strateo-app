<?php

namespace App\Http\Middleware\CRUD\TaxValueParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Rules\TaxValueDeleteRule;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_value_ids' => 'required_without:tax_value_id|array|not_in:1|distinct',
            'tax_value_ids.*' => ['integer','exists:tax_values,id',new TaxValueDeleteRule(),'distinct'],
            'tax_value_id' => 'required_without:tax_value_ids|integer|exists:tax_values,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
