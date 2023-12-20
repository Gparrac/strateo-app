<?php

namespace App\Http\Middleware\CRUD\EnterpriseParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Third;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $company = Company::first();
        if(!$company){
            return ['error' => TRUE, 'message' => 'Entreprise not exist', 'statusResponse' => 404];
        }

        return ['error' => FALSE];
    }
}