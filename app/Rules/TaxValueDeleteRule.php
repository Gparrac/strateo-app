<?php

namespace App\Rules;

use App\Models\Tax;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class TaxValueDeleteRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $taxes = Tax::whereHas('taxValues', function($query) use ($value){
            $query->where('tax_values.id',$value);
        })->get();
        if(count($taxes) > 0 ){
            $fail('El porcentaje con id ' . $value . ' presenta relación con impuestos registrados.');
        }
    }
}
