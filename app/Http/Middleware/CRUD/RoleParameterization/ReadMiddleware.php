<?php

namespace App\Http\Middleware\CRUD\RoleParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Third;
use App\Models\User;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'role_id' => 'integer',
        ]);


        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
