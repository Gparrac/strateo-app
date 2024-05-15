<?php

namespace App\Http\Middleware\CRUD\OfficeParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\User;
use App\Rules\deleteRecordsValidationRule;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'office_id' => ['required_without:office_ids', 'integer', 'exists:offices,id', new deleteRecordsValidationRule(new User(), 'office_users.office_id', 'offices','Oficina', 'usuarios')],
            'office_ids' => 'required_without:office_id|array',
            'office_ids.*' => ['integer','exists:offices,id', new deleteRecordsValidationRule(new User(), 'office_users.office_id', 'offices','Oficina', 'usuarios')],
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
