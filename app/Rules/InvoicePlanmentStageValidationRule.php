<?php

namespace App\Rules;

use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Planment;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InvoicePlanmentStageValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request = request();
        $planment = Invoice::find($request['invoice_id'])->planment;
        if ($request->stage == 'QUO') {
            if ($request['stage'] == 'CON' && $request['payOff'] <= 0) {
                $fail('Para confirmar este evento es necesario hacer un abono.');
            }else{
                $request->merge(['stage' => 'CAN']);
            }
        }
    }
}
