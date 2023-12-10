<?php

namespace App\Http\Middleware\CRUD\UserParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,CE,PASAPORTE',
            'identificacion' => 'required|digits_between:7,10',
            'names' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email',
            'email2' => 'email',
            'city_id' => 'required|exists:cities,id',
            'offices_id' => 'required|array',
            'offices_id.*' => 'integer|exists:offices,id',

            //Company users
            'name' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:A,I',
            'password' => 'required|string'
        ]);


        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        $user = Auth::user();
        if ($user->third_id !== null) {
            return ['error' => TRUE, 'message' => 'third exists'];
        }

        return ['error' => FALSE];
    }
}