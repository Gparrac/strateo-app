<?php

namespace App\Http\Middleware\CRUD\CategoryParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_ids' => 'required_without:category_id|array',
            'category_ids.*' => 'integer|exists:categories,id|distinct',
            'category_id' => 'required_without:category_ids|integer|exists:categories,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
