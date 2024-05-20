<?php

namespace App\Http\Middleware\CRUD\PaymentMethodParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Warehouse;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'payment_method_id' => 'required|exists:payment_methods,id',
            'name' => 'required|string|min:3|max:80',
            'description' => 'string',
            'status' => 'required|in:A,I'
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        $names = $request->input('names');
        $surnames = $request->input('surnames');
        $business_name = $request->input('business_name');

        if($names && $surnames && $business_name){
            return ['error' => TRUE, 'message' => 'too much names fields for request'];
        }

        return ['error' => FALSE];
    }
}
