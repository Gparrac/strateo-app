<?php

namespace App\Http\Middleware\Metrics;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Rules\ProductGreatestDateValidation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class CustomerMiddleware implements ValidateData
{

    public function validate(Request $request)
    {
        $rules = [
            'option' => 'required|in:TA,LA,LS',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date'=> ['required','date_format:Y-m-d H:i:s', new ProductGreatestDateValidation($request->input('start_date'))]
        ];
        if($request['option'] =='LA'){
            $rules = array_merge($rules, [
                'keyword' => 'string|min:3|max:50',
                'pagination' => 'numeric|min:3|max:50',
            ]);
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }

}
