<?php

namespace App\Http\Middleware\CRUD\EnterpriseParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identificacion' => 'required|numeric|digits_between:7,10',
            'verification_id' => 'required|numeric|digits_between:7,10',
            'names' => 'string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email',
            'email2' => 'email',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'required|numeric',

            //Company Table
            'path_logo' => 'required|string',
            'header' => 'string',
            'footer' => 'string'
        ]);

        if ($validator->fails()){
            return [
                'error' => TRUE,
                'message' => $validator->errors()
            ];
        }

        $names = $request->input('names');
        $surnames = $request->input('surnames');
        $business_name = $request->input('business_name');

        if($names && $surnames && $business_name){
            return response()->json(['error' => 'too much names fields for request'], 400);
        }

        return ['error' => FALSE];
    }
}