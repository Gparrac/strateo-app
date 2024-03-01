<?php

namespace App\Http\Middleware\CRUD\SupplierParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\Field;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Monolog\Handler\PushoverHandler;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //--------------------- new attributes
            'commercial_registry' => 'required|string|max:50',
            'commercial_registry_file' => 'required|file|mimes:pdf,docx|max:2048',
            'rut_file' => 'required|file|mimes:pdf,docx|max:2048',
            'note' => 'required|string',
            'status' => 'required|in:A,I',
            //--------------------- third attributes
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => 'required|numeric|digits_between:7,10|unique:thirds,identification',
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'required_without:names,surnames|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => 'required|email|unique:thirds,email',
            'email2' => 'email|different:email',
            'postal_code' => 'required|numeric',
            'city_id' => 'required|exists:cities,id',
            'code_ciiu_id' => 'required|exists:code_ciiu,id',
            'secondary_ciiu_ids' => 'array',
            'secondary_ciiu_ids.*' => 'numeric|exists:code_ciiu,id',
            // //--------------------- service attributes
            'services' => ['required', 'array'],
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.fields' => ['required', 'array'],
            'services.*.fields.*.field_id' => 'required|exists:fields,id',
            'services.*.fields.*.content' => 'required'
            //--------------------- others
        ]);

        if ($validator->fails()) {
            return ['error' => TRUE, 'message' => $validator->errors()];
        }

        $contentRules = [];

        $recordServices = $request['services'];
        foreach ($recordServices as  $skey => $service) {
            if (count($service['fields']) != Service::find($service['service_id'])->fields()->count()) {
                return ['error' => TRUE, 'message' => ['Campos' => 'La cantidad de campos no coincide con el servicio seleccionado.']];
            }
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
}
