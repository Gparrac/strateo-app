<?php

namespace App\Http\Middleware\CRUD\RoleParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            //Third table

            'name' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'description' => 'string|max:300|regex:/^[\p{L}\s]+$/u',
            'forms' => 'required|array',
            'forms.*.form_id' => 'required|integer|exists:forms,id|distinct',
            'forms.*.permissions_id' => 'array',
            'forms.*.permissions_id.*' => 'integer|exists:permissions,id|distinct',

        ]);


        if ($validator->fails()) {
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
