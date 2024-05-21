<?php

namespace App\Http\Middleware\CRUD\ServiceParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //--------------------- new attributes
            'name' => 'required|string|min:3|max:80',
            'status' => 'required|in:A,I',
            'description' =>'required|string|min:3|max:3000',
            'fields' => 'required|array',
            'fields.*.field_id' => 'required|exists:fields,id|distinct',
            'fields.*.required' => 'required|in:1,2',

            //--------------------- others
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
