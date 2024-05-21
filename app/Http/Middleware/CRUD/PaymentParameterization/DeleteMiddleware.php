<?php

namespace App\Http\Middleware\CRUD\PaymentParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_ids' => 'required_without:payment_method_id|array|not_in:1|distinct',
            'payment_method_ids.*' => 'integer|exists:payment_methods,id|distinct',
            'payment_method_id' => 'required_without:payment_method_ids|integer|exists:payment_methods,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
