<?php

namespace App\Http\Middleware\CRUD\InvoiceParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Validator;

class DeleteMiddleware implements ValidateData
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_ids' => 'required_without:invoice_id|array',
            'invoice_ids.*' => 'integer|exists:invoices,id|distinct',
            'invoice_id' => 'required_without:invoice_ids|integer|exists:invoices,id',
        ]);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }
        return ['error' => FALSE];
    }
}
