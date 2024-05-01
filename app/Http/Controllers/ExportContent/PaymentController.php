<?php

namespace App\Http\Controllers\ExportContent;

use App\Exports\InventoryTradeExport;
use App\Exports\PaymentExport;
use App\Http\Controllers\Controller;
use App\Models\EmployeePlanment;
use App\Models\InventoryTrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    public function __invoke(Request $request)
    {

        return Excel::download(new PaymentExport, 'inventoryTrades.xlsx');
    }
}
