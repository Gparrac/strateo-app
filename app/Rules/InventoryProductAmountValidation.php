<?php

namespace App\Rules;

use App\Models\Inventory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class InventoryProductAmountValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $typeTrade = request()->input("transaction_type");
        if($typeTrade === 'D'){
            $index = explode('.', $attribute)[1];
            $warehouseId = request()->input('warehouse_id');
            $productId = request()->input("products.{$index}.product_id");
            $inventory = Inventory::where('warehouse_id', $warehouseId)->where('product_id',$productId)->first();
            if( $inventory !== null && $inventory['stock'] < $value){
                $fail('No hay suficiente inventario para completar la salida.');
            }
        }
    }
}
