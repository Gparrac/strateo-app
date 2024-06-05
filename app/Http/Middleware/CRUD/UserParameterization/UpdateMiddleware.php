<?php

namespace App\Http\Middleware\CRUD\UserParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,CE,PASAPORTE,NIT',
            'names' => ['string','min:3','max:40','regex:/^[\p{L}\s]+$/u',Rule::when($request->type_document != 'NIT', ['required'])],
            'surnames' => ['string','min:3','max:40','regex:/^[\p{L}\s]+$/u',Rule::when($request->type_document != 'NIT', ['required'])],
            'business_name' => ['string','min:3','max:40','regex:/^[\p{L}\s]+$/u',Rule::when($request->type_document == 'NIT', ['required'])],
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email',
            'email2' => 'email',
            'city_id' => 'required|exists:cities,id',
            'offices_id' => 'required|array',
            'offices_id.*' => 'integer|exists:offices,id|distinct',

            //Users table
            'user_id' => 'required|exists:users,id,not_in:1,22',
            'name' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:A,I',
            'password' => 'string',
            'identification' => ['required','string','min:5','max:12', Rule::unique('thirds', 'identification')->ignore(User::find($request['user_id'])->third->id),],
        ]);
        if ($validator->fails()){
            return [
                'error' => TRUE,
                'message' => $validator->errors()
            ];
        }

        return ['error' => FALSE];
    }
}
