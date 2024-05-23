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
        $company = Company::with(['third' => function ($query){
            $query->select('id','type_document','identification','code_ciiu_id','verification_id','names','surnames','business_name','address','mobile','email','email2','postal_code','city_id');
            $query->with('ciiu:id,code,description');
        }])->first();
        if(!$company){
            return ['error' => TRUE, 'message' => 'Enterprise not exist', 'statusResponse' => 404];
        }

        return ['error' => FALSE];
    }
}
