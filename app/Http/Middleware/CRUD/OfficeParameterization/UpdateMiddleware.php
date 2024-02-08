<?php

namespace App\Http\Middleware\CRUD\OfficeParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Office;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $userId = auth()->id() || 1;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:40',
            'address' => 'required|string',
            'phone' => 'required|numeric|digits_between:10,13',
            'city_id' => 'required|exists:cities,id',
            'status' => 'required|in:A,I',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        try {
            $office = Office::where('id', $request->input('office_id'))
                            ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ['error' => TRUE, 'message' => 'Office not found'];
        }

        return ['error' => FALSE];
    }
}
