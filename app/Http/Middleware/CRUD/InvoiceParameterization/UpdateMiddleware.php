<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use App\Rules\InvoicePlanmentStageValidationRule;
use App\Rules\InvoiceProductWarehouseValidatiorRule;
use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;
use App\Rules\ProductGreatestDateValidation;
use Illuminate\Validation\Rule;

class UpdateMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $thirdEmailRule = Rule::unique('thirds', 'email');
        $thirdIdentificationRule = Rule::unique('thirds', 'identification');
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            //Third table
            'client_id' => 'required|exists:clients,id',
            //seller table
            'seller_id' => 'required|exists:users,id',
            //invoice Table
            'further_discount' => 'required|numeric|min:0|max:9999999',
            'state_type' => 'required|in:P,E',
            'note' => 'string',
            // -- planments table
            'start_date' => 'required_if:state_type,E|date_format:Y-m-d H:i:s',
            'end_date' => ['required_if:state_type,E', 'date_format:Y-m-d H:i:s', new ProductGreatestDateValidation($request->input('start_date'))],
            'pay_off' => 'required_if:state_type,E|numeric|min:1|max:99999999',
            'stage' => ['required_if:state_type,E', new InvoicePlanmentStageValidationRule()],



        ]);

        if ($validator->fails()) {
            return ['error' => TRUE, 'message' => $validator->errors()];
        }


        return ['error' => FALSE];
    }
}
