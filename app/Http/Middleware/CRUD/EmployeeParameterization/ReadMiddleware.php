<?php

namespace App\Http\Middleware\CRUD\EmployeeParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class ReadMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        if($request->has('employee_id')){
            $validator = Validator::make($request->all(), [
                'employee_id' => 'numeric|exists:employees,id'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                //pagination an filters
                'page' => 'numeric|min:0',
                'pagination' => 'numeric|max:100',
                'filters' => 'array',
                'filters.*.key' => 'required|in:id,name,status|distinct',
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
