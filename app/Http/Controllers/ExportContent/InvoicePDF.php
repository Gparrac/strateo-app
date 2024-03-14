<?php

namespace App\Http\Controllers\ExportContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Models\Invoice;

class InvoicePDF extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $invoiceId = 26;
        $invoice = Invoice::where('id', $invoiceId)
                    ->with('client', 'products')
                    ->get();
        return $invoice;

        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y'),
        ];
        // Header
        

        // Client information
        
        
        $pdf = PDF::loadView('PDF.invoice', $data);

        return $pdf->download('itsolutionstuff.pdf');
    }
}
