<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use App\Rules\Invoice\ProductInvoiceValidation;
use App\Rules\ProductGreatestDateValidation;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;

use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'third_id' => 'exists:thirds,id',
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
            'products.*.cost'=> 'numeric|min:1|max:99999999',
            'products.*.discount'=> 'numeric|min:1|max:99999999',
            'products.*.amount'=> 'numeric|min:1|max:9999',
            'products.*.taxes' => 'array',
            'products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'products.*.taxes.*.cost' => 'required|numeric|min:1|max:99999999',
            // -- planments table
            'start_date' => 'required_if:state_type,E|date_format:Y-m-d H:i:s',
            'end_date'=> ['required_if:state_type,E','date_format:Y-m-d H:i:s', new ProductGreatestDateValidation($request->input('start_date'))],
            'pay_off' => 'required_if:state_type,Enumeric|min:1|max:99999999',

            // -- plan if puchase type is e
            'furhter_products' => 'required_if:state_type,E|array',
            'furhter_products.*.product_id' => 'required|exists:products:id',
            'furhter_products.*.cost'=> 'numeric|min:1|max:99999999',
            'furhter_products.*.discount'=> 'numeric|min:1|max:99999999',
            'furhter_products.*.amount'=> 'numeric|min:1|max:9999',
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
