<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Metrics\Services\ClientAnalytics;
use App\Http\Controllers\Metrics\Services\SellerAnalytics;
use App\Http\Controllers\Metrics\Interfaces\ContextService;
use App\Http\Controllers\Metrics\Services\InvoiceAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetricHandler extends Controller
{
    public function __invoke(Request $request, $type)
    {
        switch($type){
            case 'client':
                $strategy = new ContextService(new ClientAnalytics());
                break;
            case 'seller':
                $strategy = new ContextService(new SellerAnalytics());
                break;
            case 'invoice':
                $strategy = new ContextService(new InvoiceAnalytics());
                break;
            default:
                return response()->json(['error' => 'Method not allowed']);
        }

        return $strategy->execResource($request);


    }
}
