<?php

namespace App\Http\Middleware\CRUD\FieldParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\Field;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //--------------------- new attributes
            'field_id' => 'required|exists:fields,id',
            'name' => ['required','string', Rule::unique('fields', 'name')->ignore(Field::find($request['field_id']))],
            'type' =>'required|in:F,T,A,I',
            'length' => 'number',
            'status' =>'required|in:A,I',
            //--------------------- others
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
