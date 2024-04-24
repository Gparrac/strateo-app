<?php

namespace App\Http\Middleware\CRUD\ProductParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\Product;
use App\Rules\InvoiceProductValidationRule;
use App\Rules\ProductPlanmentValidationRule;
use App\Rules\ProductSubproductValidation;
use App\Rules\ProductTracingRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    protected $rules = [];
    public function validate(Request $request)
    {
        if($request->has('type_connection')){
            $this->typeConnectionValidation($request->input('type_connection'));
        }else{
            $this->createProductValidation();
        }

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }


    protected function createProductValidation(){
        $this->rules = [
            'product_id'  => ['required', 'exists:products,id'],
            'type' => 'required|in:T,I', // Tangible, Intangible
            'type_content' => 'required|in:E,C,L,R',//Evento, Consumible, Lugar, Reutilizable
            'consecutive' => ['required', 'numeric', Rule::unique('products', 'consecutive')->ignore(request()->input('product_id'))],
            'name' => 'required|string|min:3|max:50',
            'description' => 'string|min:3|max:250',
            'cost' => 'required|numeric|min:0',
            'product_code' => ['string', 'min:3', 'max:100', Rule::unique('products', 'product_code')->ignore(request()->input('product_id'))],
            'brand_id' => 'required|exists:brands,id',
            'measure_id' => 'required|exists:measures,id',
            'barcode' => 'string|min:3|max:100',
            'photo1' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photo2' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photo3' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:A,I',
            'size' => 'required|string|max:100',

            //products
            'products' => 'array',
            'products.*.product_id' => ['required', new ProductSubproductValidation(request()->input('type'), request()->input('type_content'))],
            'products.*.amount' => 'required|integer',
            'categories_id' => 'required|array',
            'categories.*' => 'required|exists:categories,id',
            'taxes' => 'array',
            'taxes.*.tax_id' => 'required|exists:taxes,id',
            'taxes.*.percent' => 'required|numeric|min:0|max:100',
            'tracing' => 'required_if:type,T|boolean',
            'libretto_activity_ids' => 'array',
            'libretto_activity_ids*' => 'required|exists:libretto_activities,id',
        ];
    }
    protected function typeConnectionValidation($typeConnection){

        $this->rules = [
            'type_connection' => 'required|in:I,F,E,S',
            'products' => 'required|array',
            'products.*.product_id' => ['required','exists:products,id', new InvoiceProductValidationRule($typeConnection)],
            'products.*.cost' => 'required|numeric|min:1|max:99999999',
            'products.*.discount' => 'numeric|min:1|max:99999999',
            'products.*.amount' => 'required|numeric|min:1|max:9999',
            'products.*.taxes' => 'array',
            'products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'products.*.taxes.*.percent' => 'required|numeric|min:1|max:99',
            'invoice_id' => 'required|exists:invoices,id'
        ];
        if ($typeConnection == 'F' || $typeConnection = 'I') {
            Log::info('entry validation###');
            Log::info($typeConnection);
            array_merge($this->rules, [
                'products.*.warehouse_id' => 'exists:warehouses,id',
                'products.*.tracing' => 'required|boolean',
                'products.*.events' => 'required|array',
                'products.*.events.*.product_id' => 'required|exists:products,id',
                'products.*.events.*.amount' => 'required|numeric|min:1|max:9999'

            ]);
        }
        if($typeConnection == 'E') {
            Log::info('entry validationLolll');
            $this->rules = array_merge($this->rules, [
                'subproducts' => 'array',
                'subproducts.*.product_id' => ['required','exists:products,id'],
                'subproducts.*.amount' => 'required|numeric|min:1|max:9999',
                'subproducts.*.events' => 'required|array',
                'subproducts.*.events.*.product_id' => 'required|exists:products,id',
                'subproducts.*.tracing' => 'required|boolean',
                'subproducts.*.warehouse_id' => [ProductTracingRule::class]
            ]);
        }

    }
}
