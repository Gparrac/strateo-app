<?php

namespace App\Http\Controllers\ExportContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\ProductInvoice;
use App\Http\Utils\PriceFormat;
use App\Models\FurtherProductPlanment;
use App\Models\Planment;
use App\Models\ProductPlanment;

class InvoicePDF extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $invoiceId = 43;
        $invoice = Invoice::findOrFail($invoiceId);

        //Client Information
        $client = $invoice->client->third;
        $furtherProducts = null;
        $furtherProductsPurchase = null;
        // Products with tax
        if($invoice->sale_type['id'] == 'P'){
            $titlePDF = 'Productos Contratados';
            $products = $this->getProducts(ProductInvoice::where('invoice_id', $invoice['id']));
            $productsPurchase = $this->getTotalPurchase($products);
            $products = $this->setTotalTax($products);
        }else{
            $titlePDF = 'Eventos contratados';
            $planmentId = Planment::where('invoice_id', $invoiceId)->first()->id;
            $products = $this->getProducts(ProductPlanment::where('planment_id', $planmentId));
            $furtherProducts = $this->getProducts(FurtherProductPlanment::where('planment_id', $planmentId));
            $products = $this->setTotalTax($products);
            $furtherProducts = $this->setTotalTax($furtherProducts);
            $productsPurchase = $this->getTotalPurchase($products);
            $furtherProductsPurchase = $this->getTotalPurchase($furtherProducts);
            $productsPurchase['net_total'] =  PriceFormat::getNumber($furtherProductsPurchase['unformat_total_purchase'] + $productsPurchase['unformat_total_purchase']);
        }


        //Set total taxes for each tax


        //products Total Purchase

        // Company Header and Footer
        $dataPDF = Company::first();
        // return $products;
        $pdf = PDF::loadView('PDF.invoice', compact('dataPDF', 'titlePDF', 'client', 'invoice', 'products', 'productsPurchase', 'furtherProductsPurchase'));

        return $pdf->download('itsolutionstuff.pdf');
    }

    private function getProducts( $query)
    {
        return $query
        ->with('product', 'taxes')
        ->get();
    }
    private function getEventProducts($invoiceId)
    {
        $planmentId = Planment::where('invoice_id', $invoiceId)->first()->id;
        return ProductPlanment::where('planment_id', $planmentId)->with('product','taxes')->get();
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
            'total_purchase' => PriceFormat::getNumber($totalTaxProduct + $totalProduct),
            'unformat_total_purchase' => $totalTaxProduct + $totalProduct
        ];
    }
}
