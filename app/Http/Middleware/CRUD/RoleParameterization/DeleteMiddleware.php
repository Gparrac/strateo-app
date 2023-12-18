<?php

namespace App\Http\Middleware\CRUD\RoleParameterization;

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
            'roles_id' => 'required|array',
            'roles_id.*' => 'integer|exists:roles,id',

        ]);


        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
