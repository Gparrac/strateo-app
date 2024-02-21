<?php

namespace App\Rules;

use App\Models\Inventory;
use App\Models\Planment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductPlanmentValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request = request();
        $planment = Planment::find($request->input("planment_id"));
        $productIdAttr = $request->input(str_replace('.amount', '.product_id', $attribute));
        $warehouseIdIdAttr = $request->input(str_replace('.amount', '.warehouse_id', $attribute));

        if ( in_array($planment->state, ['CON','REA', 'FIN'])) {
                //validate products' stock available
                        $inventory = Inventory::where('product_id', $productIdAttr)->where('warehouse_id', $warehouseIdIdAttr)->first();
                        if ($inventory && $inventory->stock  < $value) {
                            $fail('Uno de los productos no cuenta con inventario suficiente');
                        }

                //valitade employees with schedule to set off
            }
    }
}
