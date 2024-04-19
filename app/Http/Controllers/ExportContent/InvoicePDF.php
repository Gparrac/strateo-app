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
use App\Models\Tax;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;

class InvoicePDF extends Controller
{
    private $taxesAdded = [];
    private $productsTaxesAdded = [];
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //$invoiceId = 46;  // 43 => event, 22 => straight purchase
        $invoice = Invoice::with(['taxes:id,name,acronym','client'=>function($query){
            $query->with(['third' => function($query){
                $query->with(['city:id,name']);
                $query->select('thirds.id','names','surnames','identification','email','mobile','business_name','city_id','address');
            }]);
            $query->select('clients.id','clients.third_id');
        }, 'seller' => function($query){
            $query->with(['third:id,names,surnames']);
            $query->select('users.id','users.third_id');
        }])->findOrFail($request['invoice_id']);

        //Client Information
        // $client = Third::with(['city:id,name'])->select('id','names','surnames','identification','email','mobile','business_name')->findOrFail($invoice->client->third->id);
        $furtherProducts = null;
        $furtherProductsPurchase = null;
        $planment = Planment::where('invoice_id', $request['invoice_id'])->select()->first();

        // Products with tax
        if($invoice->sale_type['id'] == 'P'){
            $titlePDF = 'Productos Contratados';
            $products = $this->getProducts(ProductInvoice::where('invoice_id', $invoice['id']));
            $products = $this->setTotalTax($products);
            $productsPurchase = $this->getTotalPurchase($products);
        }else{
            $titlePDF = 'Eventos contratados';
            $products = $this->getProducts(ProductPlanment::select('cost', 'discount','products_planments.id','product_id','id', 'planment_id','amount')->where('planment_id', $planment['id']));
            $furtherProducts = $this->getProducts(FurtherProductPlanment::where('planment_id', $planment['id']));
            $products = $this->setTotalTax($products);
            $productsPurchase = $this->getTotalPurchase($products);
            $furtherProducts = $this->setTotalTax($furtherProducts);
            $furtherProductsPurchase = $this->getTotalPurchase($furtherProducts);
            $productsPurchase['total_product'] =  $furtherProductsPurchase['total_product'] + $productsPurchase['total_product'];
            $productsPurchase['total_purchase'] =  $furtherProductsPurchase['total_purchase'] + $productsPurchase['total_purchase'];
            $productsPurchase['total_tax_product'] = $productsPurchase['total_tax_product'] + $furtherProductsPurchase['total_tax_product'];
        }
        $headTaxes = Tax::whereIn('id',$this->taxesAdded)->select('id','name','acronym')->get();
        $productTaxes = $this->productsTaxesAdded;
        $this->setGlobalTaxes($invoice, $productsPurchase);

        $dataPDF = Company::with(['third' =>  function($query){
            $query->with('city:id,name')->select('thirds.id','names','surnames','type_document','identification','business_name','address','mobile','email','postal_code','city_id');
        }])->first();

        // Company Header and Footer
        // return compact('dataPDF', 'titlePDF', 'client', 'invoice', 'products', 'productsPurchase', 'furtherProducts', 'furtherProductsPurchase');
        $pdf = PDF::loadView('PDF.invoiceTemplateV2', compact('productTaxes','planment', 'headTaxes', 'dataPDF', 'titlePDF', 'invoice', 'products', 'productsPurchase', 'furtherProducts', 'furtherProductsPurchase'));
        return $pdf->download('invoice.pdf');
    }

    private function getProducts( $query)
    {
        return $query
        ->with('product:id,name', 'taxes:id,name,acronym')
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

                $index = array_search($tax->id, $this->taxesAdded);

                $templateProduct = [
                    'id' => $product['product']['id'],
                    'name' => $product['product']['name'],
                    'percent' => $tax['pivot']['percent'],
                    'total' => $tax['total_tax']
                ];
                if($index !== false){
                    array_push($this->productsTaxesAdded[$index], $templateProduct);
                }else{
                    array_push($this->taxesAdded, $tax['id']);
                    $newIndex = count($this->taxesAdded) - 1;
                    $this->productsTaxesAdded[$newIndex] = [];
                    array_push($this->productsTaxesAdded[$newIndex], $templateProduct);
                }
            });

            $product->total_tax_product = $totalTaxProduct;
            return $product;
        });
    }

    private function getTotalPurchase($products)
    {
        $totalTaxProduct = 0;
        $totalProduct = 0;
        $totalDiscount = 0;
        $products->each(function ($product) use(&$totalProduct, &$totalTaxProduct, &$totalDiscount){
            $totalProduct += $product['total'];
            $totalTaxProduct += $product['total_tax_product'];
            $totalDiscount += $product['discount'];

            //This will help us know if the IVA is only printed once.
            // if(count($product->taxes) > 1 && isset($product->taxes[0]) && $product->taxes[0]->acronym == 'IVA' && $onlyIva) $onlyIva = FALSE;
        });
        return [
            'total_tax_product' => $totalTaxProduct,
            'total_product' => $totalProduct,
            'total_purchase' => $totalTaxProduct + $totalProduct,
            'total_discount' => $totalDiscount
        ];
    }
    private function setGlobalTaxes($invoice, &$totalPurchase){
        $totalTaxes = 0;
        $invoice->taxes->each(function($tax) use (&$totalPurchase, &$totalTaxes){
            $tax['total'] = $totalPurchase['total_product'] * $tax['pivot']['percent'] / 100;
            $totalTaxes += $tax['total'];
        });
        $totalPurchase['total_global_taxes'] = $totalTaxes;
        $totalPurchase['total_purchase'] -= $totalTaxes;

    }
}
