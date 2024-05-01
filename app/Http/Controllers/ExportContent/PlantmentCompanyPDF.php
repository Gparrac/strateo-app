<?php

namespace App\Http\Controllers\ExportContent;

use App\Http\Controllers\Controller;
use App\Http\Utils\FileFormat;
use App\Models\Company;
use App\Models\EmployeePlanment;
use App\Models\Invoice;
use App\Models\LibrettoActivity;
use App\Models\Planment;
use App\Models\Product;
use App\Models\ProductInvoice;
use App\Models\SubproductPlanment;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;

class PlantmentCompanyPDF extends Controller
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

        $employees = EmployeePlanment::with(['employee' =>  function ($query) {
            $query->with('third:id,names,surnames,business_name,identification,type_document');
            $query->select('employees.id', 'third_id');
        },'charges:id,name','paymentMethod:id,name'])->where('planment_id', $planment->id)->select()->get();
        // Subproducts
        $products = SubproductPlanment::with(['product' => function ($query) {
            $query->with(['measure:id,symbol', 'brand:id,name']);
            $query->select('products.id','products.size', 'products.name', 'products.consecutive', 'products.product_code', 'products.brand_id', 'products.measure_id', 'products.cost as defaultCost');
        },'productPlanments', 'warehouse' => function ($query) {
            $query->with('city:id,name')->select('id', 'city_id', 'address');
        }])->where('planment_id', $planment['id'])->get();
        $products->each(function ($product, $key) use ($products) {
            $events = Product::join('products_planments', 'products.id', 'products_planments.product_id')
            ->join('product_planments_subproduct_planments','product_planments_subproduct_planments.product_planment_id','products_planments.id')
            ->where('product_planments_subproduct_planments.subproduct_planment_id', $product->id)
            ->select('products.id','products.name', 'product_planments_subproduct_planments.amount')->get();
            $temp = $product['product']->toArray() + [
                'events' => $events
            ];
            $products[$key] = $temp;
            });
            $dataPDF = Company::with(['third' =>  function($query){
                $query->with(['city:id,name','ciiu:id,code,description'])->select('thirds.id','names','code_ciiu_id','surnames','type_document','identification','business_name','address','mobile','email','postal_code','city_id');
            }])->first();
            $indexPublic = strpos($dataPDF['path_logo'], 'uploads');
            // $dataPDF['path_logo'] = substr($dataPDF['path_logo'], $indexPublic);
            $dataPDF['path_logo2'] = substr($dataPDF['path_logo'], $indexPublic);
        // Employees
        // dd($planment);
        $employees = $employees->map(function ($employee, $key)  {
            return [
                'id' => $employee['employee']['id'],
                'fullname' => $employee['employee']['third']['fullname'],
                'identification' => $employee['employee']['third']['type_document'] . ':' . $employee['employee']['third']['identification'],
                'charges'=> $employee['charges'],
                'salary'=> $employee['salary'],
                'payment_method' => $employee['paymentMethod']['name'],
                'reference' => $employee['reference']
            ];
        });

        //Libretto activities
        $las = LibrettoActivity::join('libretto_activities_planments', 'libretto_activities.id', 'libretto_activities_planments.libretto_activity_id')
        ->join('planments', 'planments.id', 'libretto_activities_planments.planment_id')
        ->where('planments.id', $planment['id'])
        ->select('libretto_activities.id', 'libretto_activities.name', 'libretto_activities_planments.description', 'libretto_activities_planments.path_file as pathFile')
        ->get();

        // Company Header and Footer
        // return compact('dataPDF', 'titlePDF', 'client', 'invoice', 'products', 'productsPurchase', 'furtherProducts', 'furtherProductsPurchase');
        $pdf = PDF::loadView('PDF.planmentCompanyTemplateV2', compact('planment', 'dataPDF', 'invoice', 'products','employees','las'));
        return $pdf->download('invoice.pdf');
    }





}
