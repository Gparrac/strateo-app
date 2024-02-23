<?php

namespace App\Http\Middleware\CRUD\UserParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;


class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        if($request->has('user_id')){
            $validator = Validator::make($request->all(), [
                'user_id' => 'numeric|exists:users,id'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                //pagination an filters
                'page' => 'numeric|min:0',
                'pagination' => 'numeric|max:100',
                'keyword' => 'string|max:40',
                'typeKeyword' => 'string|in:id,name,third',
                'sorters' => 'array',
                'sorters.order' => 'nullable|in:asc,desc',
            ]);
        }

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
