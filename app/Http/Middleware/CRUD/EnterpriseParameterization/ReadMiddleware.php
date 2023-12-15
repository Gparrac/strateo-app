<?php

namespace App\Http\Middleware\CRUD\EnterpriseParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Third;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        if(!$request->has('enterprise_id')) return ['error' => FALSE];

        $third = Third::find($request->input('enterprise_id'));
        if(!$third || !$third->business_name){
            return ['error' => TRUE, 'message' => 'Entreprise not exist'];
        }

        return ['error' => FALSE];
    }
}