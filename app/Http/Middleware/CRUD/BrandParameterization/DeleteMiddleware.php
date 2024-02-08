<?php

namespace App\Http\Middleware\CRUD\BrandParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required_without:brand_ids|integer|exists:brands,id',
            'brand_ids' => 'required_without:brand_id|array',
            'brand_ids.*' => 'integer|exists:brands,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
