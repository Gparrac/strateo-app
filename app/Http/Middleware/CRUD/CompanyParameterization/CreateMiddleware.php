<?php

namespace App\Http\Middleware\CRUD\CompanyParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,NIT,CE',
            'identificacion' => 'required|numeric|digits_between:7,10',
            'verification_id' => 'required|numeric|digits_between:7,10',
            'names' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email',
            'email2' => 'email',
            'postal_code' => 'required|numeric',
            'city_id' => 'required|exists:cities,id',

            //Company Table
            'path_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'header' => 'string',
            'footer' => 'string'
        ]);
        
        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        $user = Auth::user() || User::find(1);
        if ($user->third_id !== null) {
            return ['error' => TRUE, 'message' => 'third exists'];
        }

        return ['error' => FALSE];
    }
}