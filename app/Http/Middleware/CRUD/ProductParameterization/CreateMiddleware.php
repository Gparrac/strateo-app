<?php

namespace App\Http\Middleware\CRUD\ProductParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Rules\ProductSubproductValidation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
