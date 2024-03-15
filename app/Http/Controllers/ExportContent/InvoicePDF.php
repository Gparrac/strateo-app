<?php

namespace App\Http\Controllers\ExportContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\ProductInvoice;


class InvoicePDF extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $invoiceId = 22;
        $invoiceType = Invoice::findOrFail($invoiceId);

        if($invoiceType->sale_type['id'] == 'P'){
            return ProductInvoice::where('invoice_id', $invoiceId)
                ->with('product')
                ->get();
        }
        $dataPDF = Company::first();
        
        $pdf = PDF::loadView('PDF.invoice', compact('dataPDF'));
        
        return $pdf->download('itsolutionstuff.pdf');
    }
}
