<?php

namespace App\Http\Middleware\CRUD\ProductParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Rules\ProductSubproductValidation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    protected $rules;
    public function validate(Request $request)
    {
        if($request->has('typeConnection')){
            $this->typeConnectionValidation($request->input('type_connection'));
        }else{
            $this->createValidation();
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
    protected function createValidation(){
        $this->rules = [
            'type' =>'required|in:T,I',// Tangible, Intangible
            'type_content' => 'required|in:E,C,L',//Evento, Consumible, Lugar
            'consecutive' => 'required|numeric|unique:products,consecutive',
            'name' => 'required|string|min:3|max:50',
            'description' => 'string|min:3|max:250',
            'cost' => 'required|numeric|min:0',
            'product_code' => 'string|min:3|max:100|unique:products,product_code',
            'brand_id' => 'required|exists:brands,id',
            'measure_id' => 'required|exists:measures,id',
            'barcode' => 'string|min:3|max:100|unique:products,barcode',
            'size' => 'required|string|max:100',
            'photo1' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photo2' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photo3' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' =>'required|in:A,I',
            'taxes' => 'array',
            'taxes.*.tax_id' => 'required|exists:taxes,id',
            'taxes.*.porcent' => 'required|numeric|min:0|max:100',
            'supply' => 'required|boolean',
            //products
            'products' => 'array',
            'products.*.product_id' => ['required', new ProductSubproductValidation(request()->input('type'), request()->input('type_content'))],
            'products.*.amount' => 'required|integer',
            'categories_id' => 'required|array',
            'categories.*' => 'required|exists:categories,id'
        ];
    }
    protected function typeConnectionValidation($typeConnection){
        $this->rules = [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.cost' => 'required_if:state_type,P|numeric|min:1|max:99999999',
            'products.*.discount' => 'numeric|min:1|max:99999999',
            'products.*.amount' => 'numeric|min:1|max:9999',
            'products.*.taxes' => 'array',
            'products.*.taxes.*.tax_id' => 'required|exists:taxes,id',
            'products.*.taxes.*.porcent' => 'required|numeric|min:1|max:99',
        ];
        if ($typeConnection == 'F') {
            $this->rules['products.*.warehouse_id'] = 'required_if:state_type,P|exists:warehouses,id';
            $this->rules['planment_id'] = 'required|exists:planments,id';
        }
        if ($typeConnection == 'I') {
            $this->rules['products.*.warehouse_id'] = 'required_if:state_type,P|exists:warehouses,id';
            $this->rules['invoice_id'] = 'required|exists:planments,id';
        }
        if ($typeConnection == 'E') {
            $this->rules = array_merge($this->rules, [
                'planment_id' => 'required|exists:planments,id',
                'sub_products' => 'required|array',
                'sub_products.*.product_id' => 'required|exists:products,id',
                'sub_products.*.amount' => 'numeric|min:1|max:9999',
            ]);
        }
    }
}
