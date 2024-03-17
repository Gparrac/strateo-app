<?php

namespace App\Http\Controllers\ExportContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\ProductInvoice;
use App\Http\Utils\PriceFormat;

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

        // Products with tax
        $titlePDF = '';
        if($invoice->sale_type['id'] == 'P'){
            $titlePDF = 'Productos Contratados';
            $products = $this->getProducts($invoiceId);
        }

        //Set total taxes for each tax
        $products = $this->setTotalTax($products);

        //products Total Purchase
        $productsPurchase = $this->getTotalPurchase($products);

        // Company Header and Footer
        $dataPDF = Company::first();
        
        $pdf = PDF::loadView('PDF.invoice', compact('dataPDF', 'titlePDF', 'client', 'invoice', 'products', 'productsPurchase'));
        
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
            $totalTaxProduct = 0;
            $product->taxes->each(function ($tax) use ($product, &$totalTaxProduct) {
                $tax->total_tax = $product->total * ($tax->pivot->percent / 100);
                $totalTaxProduct += $tax->total_tax;
                $tax->total_tax = PriceFormat::getNumber($tax->total_tax);
            });
            $product->total_tax_product = $totalTaxProduct;
            $product->total_format = PriceFormat::getNumber($product->total);
            return $product;
        });
    }

    private function getTotalPurchase($products)
    {
        $totalTaxProduct = 0;
        $totalProduct = 0;
        $products->each(function ($product) use(&$totalProduct, &$totalTaxProduct){
            $totalProduct += $product->total;
            $totalTaxProduct += $product->total_tax_product;
            $product->total_tax_product =  PriceFormat::getNumber($product->total_tax_product);
        });
        return [
            'total_tax_product' => PriceFormat::getNumber($totalTaxProduct),
            'total_product' => PriceFormat::getNumber($totalProduct),
            'total_purchase' => PriceFormat::getNumber($totalTaxProduct + $totalProduct)
        ];
    }
}
