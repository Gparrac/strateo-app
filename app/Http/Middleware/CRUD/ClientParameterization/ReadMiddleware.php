<?php

namespace App\Http\Middleware\CRUD\ClientParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Office;
use App\Models\User;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        return ['error' => FALSE];
    }
}
