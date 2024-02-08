<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductSubproductValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $typeProduct = request()->input('type');
        if($typeProduct == 'SE'){
            if(!Product::where('id',$value)->where('type','PR')->exists()){
                $fail('Uno de los subproductos no cumple con la parametrización');
            }
            return;
        }else{
            $fail('El tipo de producto no es apto para contener subproductos');
        }

    }
}
