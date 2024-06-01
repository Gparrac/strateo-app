<?php

namespace App\Http\Controllers\Metrics\Interfaces;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContextService
{
    private FactoryService $strategy;

    public function __construct(FactoryService $strategy)
    {
        $this->strategy = $strategy;
    }

    public function execResource(Request $request)
    {
        $authId = Auth::id();
        // $request->merge([
        //     'start_date' => Carbon::parse($request->input('start_date')),
        //     'end_date' => Carbon::parse($request->input('end_date'))
        // ]);
        return $this->strategy->buildService($request, $authId);
    }
}
