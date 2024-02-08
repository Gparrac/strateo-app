<?php

namespace App\Http\Middleware\CRUD;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;

class ValidateDataMiddlewareContext 
{
    private ValidateData $strategy;

    public function __construct(ValidateData $strategy)
    {
        $this->strategy = $strategy;
    }

    public function execvalidate(Request $request)
    {
        return $this->strategy->validate($request);
    }
}