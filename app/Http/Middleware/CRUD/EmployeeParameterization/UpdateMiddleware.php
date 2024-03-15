<?php

namespace App\Http\Middleware\CRUD\EmployeeParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\Employee;
use App\Models\Field;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Third;
use App\Rules\EmployeePlanmentValidationRule;
use App\Rules\ServiceFieldSizeValidationRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    protected $rules;
    public function validate(Request $request)
    {
        $typeRequest = $request->has("type_connection");
        if($typeRequest){
            $this->typeConnectionValidation();
        }else{
            $this->updateValidation($request);
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        if(!$typeRequest){
            $contentRules = [];
            $recordServices = $request['services'] ?? [];
            Log::info('ARRAVING');
            Log::info($recordServices);
            foreach ($recordServices as  $skey => $service) {
                if (count($service['fields']) != Service::find($service['service_id'])->fields()->count()) {
                    return ['error' => TRUE, 'message' => ['Campos' => 'La cantidad de campos no coincide con el servicio seleccionado.']];
                }
                foreach ($service['fields'] as $fkey => $field) {
                    $fieldQuery =  Field::find($field['field_id']);
                    $typeField = $fieldQuery->type['id'];
                    $serviceRealeted = $fieldQuery->services()->where('services.id', $service['service_id'])->first();
                    if ($serviceRealeted['pivot']['required'] == 1 && $typeField != 'F') array_push($contentRules, 'required');
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
                            array_push($contentRules,'nullable', 'file', 'mimes:pdf,docx', 'max:2048');
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
            $request->merge(['services' => $recordServices, 'email2' => $request['email2'] ?? null]);
        }
        return ['error' => FALSE];
    }
    public function updateValidation(Request $request){
        $this->rules =
        [
            //--------------------- third attributes
            'employee_id'=> 'required|exists:employees,id',
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => ['required','numeric', 'digits_between:7,10', Rule::unique('thirds', 'identification')->ignore(Employee::find($request['employee_id'])->third->id)],
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => ['required', 'email', Rule::unique('thirds', 'email')->ignore(Employee::find($request['employee_id'])->third->id)],
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
    public function typeConnectionValidation(){
        $this->rules = [
            'invoice_id' => 'required|exists:invoices,id',
            'employees' => ['array'],
            'employees.*.employee_id' => ['required','exists:employees,id'],
            'employees.*.salary' => 'required|numeric|min:0|max:99999999'
        ];
    }
}
