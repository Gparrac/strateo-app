<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Warehouse;
use App\Rules\ProductGreatestDateValidation;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'invoice_id' => 'exists:invoices,id',
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => 'required|numeric|digits_between:7,10|unique:thirds,identification',
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'required_without:names,surnames|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email|unique:thirds,email',
            'email2' => 'email|different:email',
            'postal_code' => 'required|numeric',
            'city_id' => 'required|exists:cities,id',
            'code_ciiu_id' => 'required|exists:code_ciiu,id',
            'secondary_ciiu_ids' => 'array',
            'secondary_ciiu_ids.*' => 'numeric|exists:code_ciiu,id',
            //seller table
            'seller_id' => 'required|exists:users,id',
            //invoice Table
            'further_discount' => 'required|double',
            'state_type' => 'required|in:P,E',
            'note' => 'string',
            //-- products for purchace
            'products' => 'required|array',
            'products.*.products_id' => 'required|exists:products:id',
            'products.*.cost'=> 'required|numeric|min:1|max:99999999',
            'products.*.discount'=> 'required|numeric|min:1|max:99999999',
            'products.*.amount'=> 'required|numeric|min:1|max:9999',
            'products.*.type' => 'required|in:T,I',
            'products.*.warehouse_id' => 'required_if:products.*.type,T,exists:warehouses,id',
            //-- -- product's taxes
            'products.*.taxes' => 'array',
            'products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'products.*.taxes.*.cost' => 'required|numeric|min:1|max:99999999',
            // -- planments table
            'start_date' => 'required_if:state_type,E|date_format:Y-m-d H:i:s',
            'end_date'=> ['required_if:state_type,E','date_format:Y-m-d H:i:s', new ProductGreatestDateValidation($request->input('start_date'))],
            'pay_off' => 'required_if:state_type,Enumeric|min:1|max:99999999',

            // -- plan weather puchase type is e
            'further_products' => 'required_if:state_type,E|array',
            'further_products.*.products_id' => 'required|exists:products:id',
            'further_products.*.cost'=> 'numeric|min:1|max:99999999',
            'further_products.*.discount'=> 'numeric|min:1|max:99999999',
            'further_products.*.amount'=> 'numeric|min:1|max:9999',
            'further_products.*.warehouse_id' => 'exists:warehouses,id',
            //-- -- product's taxes
            'further_products.*.taxes' => 'array',
            'further_products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'further_products.*.taxes.*.cost' => 'required|numeric|min:1|max:99999999',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }


        return ['error' => FALSE];
    }
}
