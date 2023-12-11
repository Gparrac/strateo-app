<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
use App\Http\Controllers\CRUD\Interfaces\CRUD;

class CRUDContext 
{
    private CRUD $strategy;

    public function __construct(CRUD $strategy)
    {
        $this->strategy = $strategy;
    }

    public function execResource(Request $request)
    {
        return $this->strategy->resource($request);
    }
}