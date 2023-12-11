<?php

namespace App\Http\Middleware\CRUD\OfficeParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Office;
use App\Models\User;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $user = User::find(1);

        $third = Office::where('id', $user->third_id)->first();

        return ['error' => FALSE];
    }
}
