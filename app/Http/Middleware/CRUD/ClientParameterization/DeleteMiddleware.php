<?php

namespace App\Http\Middleware\CRUD\ClientParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'charge_ids' => 'required_without:charge_id|array',
            'charge_ids.*' => 'integer|exists:charges,id|distinct',
            'charge_id' => 'required_without:charge_ids|integer|exists:charges,id',
        ]);
        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
