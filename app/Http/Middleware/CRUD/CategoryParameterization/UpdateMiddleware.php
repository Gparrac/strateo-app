<?php

namespace App\Http\Middleware\CRUD\CategoryParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\Field;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Third;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:3|max:50',
            'code' => 'required|string|max:10',
            'status' => 'required|in:A,I',

            'products_ids' => 'array',
            'products_ids.*' => 'numeric|exists:products,id|distinct',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
