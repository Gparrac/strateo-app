<?php

namespace App\Http\Controllers\ExportContent;

use App\Exports\InventoryTradeExport;
use App\Http\Controllers\Controller;
use App\Models\InventoryTrade;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        return Excel::download(new InventoryTradeExport, 'inventoryTrades.xlsx');
    }
}
