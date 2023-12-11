<?php

namespace App\Http\Middleware\CRUD\UserParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Third;
use App\Models\User;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {

        return ['error' => FALSE];
    }
}
