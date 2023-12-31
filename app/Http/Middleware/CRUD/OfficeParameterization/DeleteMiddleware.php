<?php

namespace App\Http\Middleware\CRUD\OfficeParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        return ['error' => FALSE];
    }
}
