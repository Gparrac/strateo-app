<?php

namespace App\Http\Middleware\CRUD\InventoryParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //--------------------- trade attributes
            'inventory_trade_id' => 'required|exists:inventory_trades,id',
            'note' =>'string|min:3|max:3000',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'supplier_id' => 'required|exists:suppliers,id',
            //--------------------- inventory attributes
            //--------------------- others
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
