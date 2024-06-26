<?php

namespace App\Http\Middleware\CRUD\RoleParameterization;

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
            'role_id' => 'required|integer|exists:roles,id|not_in:1',
            'name' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'description' => 'string|max:300|regex:/^[\p{L}\s]+$/u',
            'status' => 'required|in:A,I',
            'forms' => 'required|array',
            'forms.*.form_id' => 'required|integer|exists:forms,id|distinct',
            'forms.*.permissions_id' => 'array',
            'forms.*.permissions_id.*' => 'integer|exists:permissions,id',
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
