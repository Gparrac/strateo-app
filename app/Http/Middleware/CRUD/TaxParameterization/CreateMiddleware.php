<?php

namespace App\Http\Middleware\CRUD\TaxParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => 'required|numeric|digits_between:7,10|unique:thirds,identification',
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'required_without:names,surnames|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email|unique:thirds,email',
            'email2' => 'email|different:email',
            'postal_code' => 'required|numeric',
            'city_id' => 'required|exists:cities,id',
            'code_ciiu_id' => 'required|exists:code_ciiu,id',
            'secondary_ciiu_ids' => 'array',
            'secondary_ciiu_ids.*' => 'numeric|exists:code_ciiu,id',

            //Warehouse Table
            'note' => 'required|string|min:3|max:50',
            'city_warehouse_id' => 'required|exists:cities,id',
            'address_warehouse' => 'required|string',
            'status' => 'required|in:A,I',
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
