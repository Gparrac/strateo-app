<?php

namespace App\Http\Middleware\CRUD\ClientParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Third;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => ['required','digits_between:7,10', Rule::unique('thirds', 'identification')->ignore(Third::find($request['client_id'])),],
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'required_without:names,surnames|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email|exists:thirds,email',
            'email2' => 'email|different:email',
            'postal_code' => 'required|numeric',
            'city_id' => 'required|exists:cities,id',
            'client_id' => 'required|exists:clients,id',
            'code_ciiu_id' => 'required|exists:code_ciiu,id',
            //Client table
            'commercial_registry' => 'required|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'commercial_registry_file' => 'file|mimes:pdf,docx|max:2048',
            'rut_file' => 'file|mimes:pdf,docx|max:2048',
            'legal_representative_name' => 'required|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'legal_representative_id' => 'required|string|min:3|max:80',
            'note' => 'required|string|min:3|max:80',
            'status' => 'required|in:A,I',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
