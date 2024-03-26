<?php

namespace App\Http\Controllers\ExportContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Invoice;
use App\Models\Company;
use App\Models\ProductInvoice;
use App\Http\Utils\PriceFormat;
use App\Models\FurtherProductPlanment;
use App\Models\Planment;
use App\Models\Third;
use App\Models\ProductPlanment;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


class InvoicePDF extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $invoiceId = 22;  // 43 => event, 22 => straight purchase
        $invoice = Invoice::findOrFail($invoiceId);

        //Client Information
        $client = Third::with('city', 'userCreate', 'userCreate.third')->findOrFail($invoice->client->third->id);
        $furtherProducts = null;
        $furtherProductsPurchase = null;
        // Products with tax
        if($invoice->sale_type['id'] == 'P'){
            $titlePDF = 'Productos Contratados';
            $products = $this->getProducts(ProductInvoice::where('invoice_id', $invoice['id']));
            $products = $this->setTotalTax($products);
            $productsPurchase = $this->getTotalPurchase($products);
        }else{
            $titlePDF = 'Eventos contratados';
            $planmentId = Planment::where('invoice_id', $invoiceId)->first()->id;
            $products = $this->getProducts(ProductPlanment::where('planment_id', $planmentId));
            $furtherProducts = $this->getProducts(FurtherProductPlanment::where('planment_id', $planmentId));
            $products = $this->setTotalTax($products);
            $furtherProducts = $this->setTotalTax($furtherProducts);
            $productsPurchase = $this->getTotalPurchase($products);
            $furtherProductsPurchase = $this->getTotalPurchase($furtherProducts);
            $productsPurchase['total_purchase'] =  PriceFormat::getNumber($furtherProductsPurchase['unformat_total_purchase'] + $productsPurchase['unformat_total_purchase']);
            $productsPurchase['total_tax_product'] = PriceFormat::getNumber($productsPurchase['unformat_total_tax'] + $furtherProductsPurchase['unformat_total_tax']);
        }

        // Company Header and Footer
        $dataPDF = Company::first();

        $pdf = PDF::loadView('PDF.invoicetemplate', compact('dataPDF', 'titlePDF', 'client', 'invoice', 'products', 'productsPurchase', 'furtherProducts', 'furtherProductsPurchase'));
        // dd($pdf);
        return $pdf->download('itsolutionstuff.pdf');
    }

    private function getProducts( $query)
    {
        return $query
        ->with('product', 'taxes')
        ->get();
    }

    private function setTotalTax($collection)
    {
        return $collection->map(function ($product)
        {
            $totalTaxProduct = 0;
            $product->fcost = PriceFormat::getNumber($product->cost);
            $product->fdiscount = PriceFormat::getNumber($product->discount);
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
        $totalDiscount = 0;
        $onlyIva = TRUE;
        $products->each(function ($product) use(&$totalProduct, &$totalTaxProduct, &$totalDiscount){
            $totalProduct += $product->total;
            $totalTaxProduct += $product->total_tax_product;
            $product->total_tax_product =  PriceFormat::getNumber($product->total_tax_product);
            $totalDiscount += $product->discount;
            
            //This will help us know if the IVA is only printed once.
            if(count($product->taxes) > 1 && isset($product->taxes[0]) && $product->taxes[0]->acronym == 'IVA' && $onlyIva) $onlyIva = FALSE; 
        });
        return [
            'only_iva' => $onlyIva,
            'total_tax_product' => PriceFormat::getNumber($totalTaxProduct),
            'total_product' => PriceFormat::getNumber($totalProduct),
            'total_purchase' => PriceFormat::getNumber($totalTaxProduct + $totalProduct),
            'total_discount' => PriceFormat::getNumber($totalDiscount),
            'unformat_total_purchase' => $totalTaxProduct + $totalProduct,
            'unformat_total_tax' => $totalTaxProduct
        ];
    }
}
