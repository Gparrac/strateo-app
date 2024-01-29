<?php

namespace App\Http\Middleware\CRUD\InventoryParameterization;

use App\Rules\InventoryPurposeValidationRule;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //--------------------- trade attributes
            'transaction_type' => 'required|in:E,D',
            'purpose' => ['required', new InventoryPurposeValidationRule],
            'status' => 'required|in:A,I',
            'note' =>'required|string|min:3|max:3000',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'supplier_id' => 'required|exists:supplier,id',
            'further_discount' => 'required|double|min:0',
            //--------------------- inventory attributes
            'warehouse_id' => 'required|exists:warehouses,id',
            'products' => 'required|array',
            'products.*.inventory_id' => 'required|exists:inventory,id',
            'products.*.cost' => 'required|double|min:0',
            'products.*.amount' => 'required|integer|min:1'

        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
