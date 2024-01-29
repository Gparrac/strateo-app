<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InventoryPurposeValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validar condiciones específicas aquí
        $transactionType = request()->input('transaction_type');
        $purpose = request()->input('purpose');

        if ($transactionType === 'E') {
            if (!in_array($purpose, ['IB', 'D', 'A'])) {
                $fail('El valor de :attribute no es válido para una entrada a inventario.');
            }
        } elseif ($transactionType === 'D') {
            if (!in_array($purpose, ['S', 'A'])) {
                $fail('El valor de :attribute no es válido para una salida de inventario.');
            }
        }
    }
}
