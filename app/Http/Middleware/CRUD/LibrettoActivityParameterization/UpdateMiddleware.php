<?php

namespace App\Http\Middleware\CRUD\LibrettoActivityParameterization;

use Illuminate\Http\Request;
use App\Http\Middleware\CRUD\Interfaces\ValidateData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Warehouse;

class UpdateMiddleware implements ValidateData
{
    protected $rules = [];
    public function validate(Request $request)
    {
        if($request->has('type_connection')){
            $this->connectInvoice();
        }else{
            $this->updateResource();
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()){
            return ['error' => TRUE, 'message' => $validator->errors()];
        }


        return ['error' => FALSE];
    }
    protected function updateResource(){
        $this->rules = [
            'libretto_activity_id' => 'required|exists:libretto_activities,id',
            'name' => 'required|string',
            'description' => 'string',
            'status' => 'required|in:A,I',
            'products_ids' => 'array',
            'products_ids.*' => 'numeric|exists:products,id',
        ];
    }
    protected function connectInvoice(){
        $this->rules = [
            'invoice_id' => 'required|exists:invoices,id',
            'libretto_activities' => 'required|array',
            'libretto_activities.*.libretto_activity_id' => 'required|exists:libretto_activities,id',
            'libretto_activities.*.description' => 'required|string'
        ];
    }
}
