<?php

namespace App\Http\Middleware\CRUD\SupplierParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use App\Models\Field;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Third;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //--------------------- new attributes
            'supplier_id' => 'required|exists:suppliers,id',
            'commercial_registry' => 'string|max:50',
            'commercial_registry_file' => 'file|mimes:pdf,docx|max:2048',
            'rut_file' => 'file|mimes:pdf,docx|max:2048',
            'note' => 'string',
            'status' => 'required|in:A,I',
            //--------------------- third attributes
            'type_document' => 'required|in:CC,NIT,CE,PASAPORTE',
            'identification' => ['required', 'string', 'min:5','max:12', Rule::unique('thirds', 'identification')->ignore(Supplier::find($request['supplier_id'])->third->id)],
            'names' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'surnames' => 'required_without:business_name|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'business_name' => 'required_without:names,surnames|string|min:3|max:80|regex:/^[\p{L}\s]+$/u',
            'address' => 'required|string',
            'mobile' => 'required|numeric|digits_between:10,13',
            'email' => ['required', 'email', Rule::unique('thirds', 'email')->ignore(Supplier::find($request['supplier_id'])->third->id)],
            'email2' => 'email|different:email',
            'postal_code' => 'numeric',
            'city_id' => 'required|exists:cities,id',
            'code_ciiu_id' => 'required_if:type_document,NIT|exists:code_ciiu,id',
            'secondary_ciiu_ids' => 'array',
            'secondary_ciiu_ids.*' => 'numeric|exists:code_ciiu,id|distinct',
            // //--------------------- service attributes
            'services' => 'array',
            'services.*.service_id' => 'required|exists:services,id|distinct',
            'services.*.fields' => 'required|array',
            'services.*.fields.*.field_id' => 'required|exists:fields,id|distinct',
            //--------------------- others
        ]);

        if ($validator->fails()) {
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        $contentRules = [];

        $recordServices = $request['services'] ?? [];
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
                        array_push($contentRules,'nullable','file', 'mimes:pdf,docx', 'max:2048');
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
        $request->merge(['services' => $recordServices, 'note' => $request['note'] ?? null]);

        return ['error' => FALSE];
    }
}
