<?php

namespace App\Http\Middleware\CRUD\PurchaseOrderParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_order,id',

            //Purchase order table
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'note' => 'required|string|min:3|max:45',
            'status' => 'required|in:A,I',
            //purchase_orders_products
            'products' => [
                'required',
                'array',
            ],
            'products.*.product_id' => [
                'required',
                'exists:products,id'
            ],
            'products.*.amount' => [
                'required',
                'numeric',
                'digits_between:1,10'
            ]
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
