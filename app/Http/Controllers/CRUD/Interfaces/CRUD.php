<?php

namespace App\Http\Controllers\CRUD\Interfaces;
use Illuminate\Http\Request;

interface CRUD
{
    public function resource(Request $request);
}