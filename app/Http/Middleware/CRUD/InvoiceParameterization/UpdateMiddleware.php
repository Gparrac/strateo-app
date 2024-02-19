<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use App\Rules\InvoiceProductWarehouseValidatiorRule;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Rules\ProductGreatestDateValidation;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $thirdEmailRule = Rule::unique('thirds', 'email');
        $thirdIdentificationRule = Rule::unique('thirds', 'identification');
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            //Third table
            'client_id' => 'required|exists:clients,id',
            //seller table
            'seller_id' => 'required|exists:users,id',
            //invoice Table
            'further_discount' => 'required|double',
            'state_type' => 'required|in:P,E',
            'note' => 'string',
            //-- products for purchace
            'products' => 'required|array',
            'products.*.products_id' => 'required|exists:products:id',
            'products.*.cost' => 'required|numeric|min:1|max:99999999',
            'products.*.discount' => 'required|numeric|min:1|max:99999999',
            'products.*.amount' => 'required|numeric|min:1|max:9999',
            'products.*.type' => 'required|in:T,I',
            'products.*.warehouse_id' => 'required_if:state_type,P,exists:warehouses,id',

            // ---- children products
            'products.*.children_products' => 'required_if:state_type,E|array',
            'products.*.children_products.*.product_id' => 'required|exists:products:id',
            'products.*.children_products.*.cost' => 'required|numeric|min:1|max:99999999',
            'products.*.children_products.*.amount' => 'required|numeric|min:1|max:9999',
            'products.*.children_products.*.warehouse_id' => ['required', 'exists:warehouses,id', new InvoiceProductWarehouseValidatiorRule()],
            //-- -- product's taxes
            'products.*.taxes' => 'array',
            'products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'products.*.taxes.*.porcent' => 'required|numeric|min:1|max:99999999',
            // -- planments table
            'start_date' => 'required_if:state_type,E|date_format:Y-m-d H:i:s',
            'end_date' => ['required_if:state_type,E', 'date_format:Y-m-d H:i:s', new ProductGreatestDateValidation($request->input('start_date'))],
            'pay_off' => 'required_if:state_type,Enumeric|min:1|max:99999999',
            'stage' => 'required|in:QUI,CON,REA,COM,CAN',

            // -- plan weather puchase type is e
            'further_products' => 'required_if:state_type,E|array',
            'further_products.*.product_id' => 'required|exists:products:id',
            'further_products.*.cost' => 'numeric|min:1|max:99999999',
            'further_products.*.discount' => 'numeric|min:1|max:99999999',
            'further_products.*.amount' => 'numeric|min:1|max:9999',
            'further_products.*.warehouse_id' => new InvoiceProductWarehouseValidatiorRule(),
            //-- -- product's taxes
            'further_products.*.taxes' => 'array',
            'further_products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'further_products.*.taxes.*.porcent' => 'required|numeric|min:1|max:99999999',
        ]);

        if ($validator->fails()) {
            return ['error' => TRUE, 'message' => $validator->errors()];
        }


        return ['error' => FALSE];
    }
}
