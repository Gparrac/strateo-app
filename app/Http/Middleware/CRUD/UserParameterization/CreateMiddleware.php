<?php

namespace App\Http\Middleware\CRUD\UserParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        Log::info('entrando post');
        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,CE,PASAPORTE',
            'identification' => 'required|digits_between:7,10|unique:thirds,identification',
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

        return ['error' => FALSE];
    }
}
