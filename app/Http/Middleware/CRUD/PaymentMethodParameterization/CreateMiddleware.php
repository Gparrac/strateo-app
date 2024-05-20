<?php

namespace App\Http\Middleware\CRUD\PaymentMethodParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'name' => 'required|string|min:3|max:80',
            'description' => 'string',
            'status' => 'required|in:A,I'
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }


        return ['error' => FALSE];
    }
}
