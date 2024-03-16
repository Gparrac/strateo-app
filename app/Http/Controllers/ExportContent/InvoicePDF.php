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
            $products = $this->getProducts($invoiceId);
        }

        $products = $this->setTotalTax($products);
        return $products;
        $dataPDF = Company::first();
        
        $pdf = PDF::loadView('PDF.invoice', compact('dataPDF'));
        
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
