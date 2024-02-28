<?php

namespace App\Rules;

use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Planment;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class InvoiceProductValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $typeConnection;
    public function __construct($typeConnection)
    {
        $this->typeConnection = $typeConnection;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $request = request();

        $invoice = Invoice::find($request->input("invoice_id"));
        $product = Product::find($value);
        switch ($this->typeConnection) {
            case 'I':
                if ($product->type['id'] != 'T' || $invoice->sale_type != 'I') {
                    $fail('El producto :attribute no es apto para este tipo de orden.');
                }
                break;
            case 'F':
                if ($product->type['id'] != 'T' || $invoice->sale_type != 'E') {
                    $fail('El producto :attribute no es apto para este tipo de orden.');
                }

                break;
            case 'E':
                if ($product->type['id'] != 'I' || $invoice->sale_type != 'E') {
                    $fail('El producto :attribute no es apto para este tipo de orden.');
                }
                break;
            default:
            $fail('Tipo de conección no valido.');
                break;
        }
        // ---------------------------


    }
}
