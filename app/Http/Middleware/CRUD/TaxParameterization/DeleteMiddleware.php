<?php

namespace App\Http\Middleware\CRUD\TaxParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_ids' => 'required_without:tax_id|array|not_in:1|distinct',
            'tax_ids.*' => 'integer|exists:taxes,id|distinct',
            'tax_id' => 'required_without:tax_ids|integer|exists:taxes,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
