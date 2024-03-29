<?php

namespace App\Http\Middleware\CRUD\MeasureParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Measure;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'measure_id' => 'required|exists:measures,id',
            'type' =>'required|in:TI,LE,WE,VO',
            'name' => 'required|string|min:3|max:50',
            'symbol' => ['required','string', 'max:3', Rule::unique('measures', 'symbol')->ignore(Measure::find($request['measure_id'])->id)],
            'status' => 'required|in:A,I',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
