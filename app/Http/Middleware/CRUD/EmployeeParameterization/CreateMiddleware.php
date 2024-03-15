<?php

namespace App\Http\Middleware\CRUD\EmployeeParameterization;

use App\Rules\ServiceFieldSizeValidationRule;
use App\Rules\ServiceFieldValidationRule;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\Field;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Monolog\Handler\PushoverHandler;

class CreateMiddleware implements ValidateData
{
    protected $rules;
    public function validate(Request $request)
    {
        $typeRequest = $request->has("type_connection");
        if ($typeRequest) {
            $this->typeConnectionValidation();
        } else {
            $this->createValidation();
        }

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        $contentRules = [];

        $recordServices = $request['services'] ?? [];
        foreach ($recordServices as  $skey => $service) {
            foreach ($service['fields'] as $fkey => $field) {
                $fieldQuery =  Field::find($field['field_id']);
                $typeField = $fieldQuery->type['id'];
                $serviceRealeted = $fieldQuery->services()->where('services.id', $service['service_id'])->first();

                if ($serviceRealeted['pivot']['required'] == 1) array_push($contentRules, 'required');
                $recordServices[$skey]['fields'][$fkey]['type'] = $typeField;

                switch ($typeField) {
                    case 'T':
                        array_push($contentRules, 'string', 'alpha');
                        break;
                    case 'A':
                        array_push($contentRules, 'string');
                        break;
                    case 'I':
                        array_push($contentRules, 'integer');
                        break;
                    case 'F':
                        array_push($contentRules, 'file', 'mimes:pdf,docx', 'max:2048');
                        break;
                    default:
                        # code...
                        break;
                }
                $validator2 = Validator::make($field, ['content' => $contentRules]);
                if ($validator2->fails()) {
                    return ['error' => TRUE, 'message' => $validator2->errors()];
                }
                $contentRules = [];
            }
        }
        $request->merge(['services' => $recordServices]);


        return ['error' => FALSE];
    }
    public function createValidation()
    {
        $this->rules = [
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => 'required|numeric|digits_between:7,10|unique:thirds,identification',
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email|unique:thirds,email',
            'email2' => 'email|different:email',
            'postal_code' => 'required|numeric',
            'city_id' => 'required|exists:cities,id',
            // -------------------------enployee attributes
            'type_contract' => 'required|in:TF,TI,OL,PS,CA,OT',
            'hire_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date_contract' => 'required|date_format:Y-m-d H:i:s',
            'rut_file' => ['file', 'mimes:pdf', 'max:2048'],
            'resume_file' => ['file', 'mimes:pdf,docx', 'max:2048'],
            'status' => 'required|in:A,I',
            // //--------------------- service attributes
            'services' => ['array'],
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.fields' => ['required', 'array', new ServiceFieldSizeValidationRule()],
            'services.*.fields.*.field_id' => 'required|exists:fields,id',
        ];
    }
    public function typeConnectionValidation()
    {
        $this->rules = [
            'planment_id' => 'required|exists:planments,id',
            'employees' => ['array'],
            'employees.*.employee_id' => ['required', 'exists:employees,id'],
            'employees.*.salary' => 'required|numeric|min:|max:99999999'
        ];
    }
}
