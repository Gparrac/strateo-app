<?php

namespace App\Http\Middleware\CRUD\ClientParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Office;
use App\Models\User;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        if($request->has('client_id')){
            $validator = Validator::make($request->all(), [
                'client_id' => 'numeric|exists:clients,id'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                //pagination an filters
                'page' => 'numeric|min:0',
                'pagination' => 'numeric|max:100',
                'filters' => 'array',
                'filters.*.key' => 'required|in:client,id,third,status|distinct',
                'filters.*.value' => 'required',
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
