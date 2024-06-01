<?php

namespace App\Http\Controllers\Metrics\Interfaces;

interface FactoryService
{
    public function buildService($request, $userId);
}
