<?php

namespace App\Http\Middleware\CRUD\CompanyParameterization;

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
        $user = Auth::user() || User::find(1);

        $third = Third::where('id', $user->third_id)->first();

        if(!$third){
            return ['error' => TRUE, 'message' => 'third not exist'];
        }

        $request->merge([
            'third' => $third, 
        ]);

        return ['error' => FALSE];
    }
}