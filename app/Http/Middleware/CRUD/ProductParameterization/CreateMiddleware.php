<?php

namespace App\Http\Middleware\CRUD\ProductParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Database\QueryException;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' =>'required|in:F,T,A,I',
            'consecutive' => 'required|numeric|max:10',
            'name' => 'required|string|min:3|max:50',
            'description' => 'required|string|min:3|max:250',
            'quantity' => 'required|numeric',
            'product_code' => 'required|string|min:3|max:100',
            'barcode' => 'required|string|min:3|max:100',

            'photo1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photo2' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photo3' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            //relationship
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
