<?php

namespace App\Http\Middleware\CRUD\PurchaseOrderParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_ids' => 'required_without:purchase_order_id|array|not_in:1|distinct',
            'purchase_order_ids.*' => 'integer|exists:purchase_order,id',
            'purchase_order_id' => 'required_without:purchase_order_ids|integer|exists:purchase_order,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
