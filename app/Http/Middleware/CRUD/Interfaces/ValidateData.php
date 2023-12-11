<?php

namespace App\Http\Middleware\CRUD\Interfaces;

use Illuminate\Http\Request;

interface ValidateData
{
    public function validate(Request $request);
}