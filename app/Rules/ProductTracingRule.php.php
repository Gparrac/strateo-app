<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\Warehouse;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductTracingRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tracing = request()->input(str_replace('.warehouse_id','.tracing', $attribute));
        if($value !== null && $tracing && Warehouse::find($value) === null){
            $fail('Si el registro lleva seguimiento de inventario se requiere una bodega valida.');
        }
    }
}
