<?php

namespace App\Http\Middleware\CRUD\RoleParameterization;

use App\Models\User;
use App\Rules\deleteRecordsValidationRule;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'roles_id' => 'required|array|not_in:1',
            'roles_id.*' => ['integer','exists:roles,id', new deleteRecordsValidationRule(new User(), 'role_id', null, 'Role', 'usuarios')],

        ]);


        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
