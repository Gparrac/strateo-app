<?php

namespace App\Http\Middleware\CRUD\ChargeParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'charge_ids' => 'required_without:charge_id|array|not_in:1|distinct',
            'charge_ids.*' => 'integer|exists:charges,id',
            'charge_id' => 'required_without:charge_ids|integer|exists:charges,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
