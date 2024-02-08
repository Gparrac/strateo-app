<?php

namespace App\Http\Middleware\CRUD\WarehouseParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_ids' => 'required_without:warehouse_id|array|not_in:1|distinct',
            'warehouse_ids.*' => 'integer|exists:warehouses,id',
            'warehouse_id' => 'required_without:warehouse_ids|integer|exists:warehouses,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
