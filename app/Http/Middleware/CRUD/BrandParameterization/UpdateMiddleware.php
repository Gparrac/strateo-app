<?php

namespace App\Http\Middleware\CRUD\BrandParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Brand;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required|exists:brands,id',
            'name' => ['required','string', 'min:3', 'max:50', Rule::unique('brands', 'name')->ignore(Brand::find($request['brand_id'])->id)],
            'code' => ['required', 'numeric', 'digits_between:1,10', Rule::unique('brands', 'code')->ignore(Brand::find($request['brand_id'])->id)],
            'status' => 'required|in:A,I',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
