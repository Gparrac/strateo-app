<?php

namespace App\Http\Middleware\CRUD\ClientParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Third table
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => ['required','min:5','max:12', Rule::unique('thirds', 'identification')->ignore(Client::find($request['client_id'])->third->id)],
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'required_without:names,surnames|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email|exists:thirds,email',
            'email2' => 'email|different:email',
            'postal_code' => 'numeric',
            'city_id' => 'required|exists:cities,id',
            'client_id' => 'required|exists:clients,id',
            'code_ciiu_id' => 'exists:code_ciiu,id',
            //Client table
            'commercial_registry' => 'string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'commercial_registry_file' => 'file|mimes:pdf,docx|max:2048',
            'rut_file' => 'file|mimes:pdf,docx|max:2048',
            'legal_representative_name' => 'string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'legal_representative_id' => 'string|min:3|max:80',
            'note' => 'string|min:3|max:80',
            'status' => 'required|in:A,I',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        return ['error' => FALSE];
    }
}
