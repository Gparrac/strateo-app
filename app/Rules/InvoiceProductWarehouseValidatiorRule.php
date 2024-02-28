<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\Warehouse;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InvoiceProductWarehouseValidatiorRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $productIdAttr = str_replace('.warehouse_id', '.product_id', $attribute);
        $product_id = request()->input($productIdAttr);
        if(Product::find($product_id)->type != "I"){
            $fail('El producto no es apto para tener un inventario.');
        }

        if (!Warehouse::where('id',$value)->exists()) {
            $fail('La bodega no existe.');
        }
    }

}
