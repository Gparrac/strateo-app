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
            'role_id' => 'required|integer|exists:roles,id',
            'name' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'description' => 'required|string|min:3|max:40|regex:/^[\p{L}\s]+$/u',
            'forms' => 'required|array',
            'forms.*.form_id' => 'required|integer|exists:forms,id',
            'forms.*.permissions_id' => 'required|array',
            'forms.*.permissions_id.*' => 'required|integer|exists:permissions,id',
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
