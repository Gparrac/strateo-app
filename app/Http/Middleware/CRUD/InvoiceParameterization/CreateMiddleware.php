<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use App\Rules\Invoice\ProductInvoiceValidation;
use App\Rules\InvoicePlanmentStageValidationRule;
use App\Rules\InvoiceProductWarehouseValidatiorRule;
use App\Rules\ProductGreatestDateValidation;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $thirdEmailRule = Rule::unique('thirds', 'email');
        $thirdIdentificationRule = Rule::unique('thirds', 'identification');
        $validator = Validator::make($request->all(), [
            //invoice Table
            'client_id' => 'required|exists:clients,id',
            'seller_id' => 'required|exists:users,id',
            'further_discount' => 'required|numeric',
            'state_type' => 'required|in:P,E',
            'note' => 'string',
            //-- seller table
            //-- products for purchace
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.cost'=> 'required_if:state_type,P|numeric|min:1|max:99999999',
            'products.*.discount'=> 'numeric|min:1|max:99999999',
            'products.*.amount'=> 'numeric|min:1|max:9999',
            'products.*.taxes' => 'array',
            'products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'products.*.taxes.*.porcent' => 'required|numeric|min:1|max:99',
            'products.*.warehouse_id'=> 'required_if:state_type,P|exists:warehouses,id',
//--------------------------- fields to invoice event
            // ---- children products
            'products.*.children_products' => 'required_if:state_type,E|array',
            'products.*.children_products.*.product_id' => 'required|exists:products,id',
            'products.*.children_products.*.cost'=> 'required|numeric|min:1|max:99999999',
            'products.*.children_products.*.amount'=> 'required|numeric|min:1|max:9999',
            'products.*.children_products.*.warehouse_id'=> new InvoiceProductWarehouseValidatiorRule(),

            // -- planments table
            'start_date' => 'required_if:state_type,E|date_format:Y-m-d H:i:s',
            'end_date'=> ['required_if:state_type,E','date_format:Y-m-d H:i:s', new ProductGreatestDateValidation($request->input('start_date'))],
            'pay_off' => 'required_if:state_type,Enumeric|min:1|max:99999999',
            // -- plan if puchase type is e
            'further_products' => 'required_if:state_type,E|array',
            'further_products.*.product_id' => 'required|exists:products,id',
            'further_products.*.cost'=> 'numeric|min:1|max:99999999',
            'further_products.*.discount'=> 'numeric|min:1|max:99999999',
            'further_products.*.amount'=> 'numeric|min:1|max:9999',
            'futher_products.*.warehouse_id' => new InvoiceProductWarehouseValidatiorRule(),
            //-- -- product's taxes
            'further_products.*.taxes' => 'array',
            'further_products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'further_products.*.taxes.*.porcent' => 'required|numeric|min:0|max:99',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }


        return ['error' => FALSE];
    }
}
