<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use App\Rules\Invoice\ProductInvoiceValidation;
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
            //Third table
            // 'third_id' => 'exists:thirds,id',
            // 'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            // 'identification' => ['required','numeric', 'digits_between:7,10',$request->has('third_id') ? $thirdIdentificationRule->ignore($request->input('third_id')) : $thirdIdentificationRule],
            // 'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            // 'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            // 'business_name' => 'required_without:names,surnames|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            // 'address' => 'required|string',
            // 'mobile' => 'required|numeric|digits_between:10,13',
            // 'email' => ['required', 'email', $request->has('third_id') ? $thirdEmailRule->ignore($request->input('third_id')) : $thirdEmailRule],
            // 'email2' => 'email|different:email',
            // 'postal_code' => 'required|numeric',
            // 'city_id' => 'required|exists:cities,id',
            // 'code_ciiu_id' => 'required|exists:code_ciiu,id',
            // 'secondary_ciiu_ids' => 'array',
            // 'secondary_ciiu_ids.*' => 'numeric|exists:code_ciiu,id',
            'client_id' => 'required|exists:clients,id',
            //invoice Table
            //-- seller table
            'seller_id' => 'required|exists:users,id',
            'further_discount' => 'required|numeric',
            'state_type' => 'required|in:P,E',
            'note' => 'string',
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
