<?php

namespace App\Http\Middleware\CRUD\LibrettoActivityParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'libretto_activity_ids' => 'required_without:libretto_activity_id|array|not_in:1|distinct',
            'libretto_activity_ids.*' => 'integer|exists:libretto_activities,id',
            'libretto_activity_id' => 'required_without:libretto_activity_ids|integer|exists:libretto_activities,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
