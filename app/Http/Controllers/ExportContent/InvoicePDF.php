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
        $invoice = Invoice::findOrFail($invoiceId);

        //Client Information
        $client = $invoice->client->third;
        $invoiceId = $invoice->id;

        // Products with tax
        $titlePDF = '';
        if($invoice->sale_type['id'] == 'P'){
            $titlePDF = 'Productos Contratados';
            $products = $this->getProducts($invoiceId);
        }

        //Set total taxes for each tax
        $products = $this->setTotalTax($products);

        $dataPDF = Company::first();
        
        $pdf = PDF::loadView('PDF.invoice', compact('dataPDF', 'titlePDF', 'client', 'invoiceId', 'products'));
        
        return $pdf->download('itsolutionstuff.pdf');
    }

    private function getProducts($invoiceId)
    {
        return ProductInvoice::where('invoice_id', $invoiceId)
        ->with('product', 'taxes')
        ->get();
    }

    private function setTotalTax($collection)
    {
        return $collection->map(function ($product)
        {
            $product->taxes->map(function ($tax) use ($product) {
                $tax->total_tax = $product->total * ($tax->pivot->percent / 100);
                return $tax;
            });
            return $product;
        });
    }
}
