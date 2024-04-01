<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use App\Rules\Invoice\ProductInvoiceValidation;
use App\Rules\InvoicePlanmentStageValidationRule;
use App\Rules\InvoiceProductWarehouseValidatiorRule;
use App\Rules\ProductGreatestDateValidation;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $thirdEmailRule = Rule::unique('thirds', 'email');
        $thirdIdentificationRule = Rule::unique('thirds', 'identification');
        $validator = Validator::make($request->all(), [
            //invoice Table
            'client_id' => 'required|exists:clients,id',
            'seller_id' => 'required|exists:users,id',
            'sale_type' => 'required|in:P,E',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'note' => 'string',
            // -- planments table
            'start_date' => 'required_if:sale_type,E|date_format:Y-m-d H:i:s',
            'end_date'=> ['required_if:sale_type,E','date_format:Y-m-d H:i:s', new ProductGreatestDateValidation($request->input('start_date'))],
            'pay_off' => 'required_if:sale_type,E|numeric|min:1|max:99999999',
            'taxes' => 'array',
            'taxes.*.tax_id' => 'required|exists:taxes,id',
            'taxes.*.percent' => 'required|numeric|between:-99,99|regex:/^-?\d+(\.\d{2,3})?$/'
       ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }


        return ['error' => FALSE];
    }
}
