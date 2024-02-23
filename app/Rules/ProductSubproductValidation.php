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
    protected $typeProduct;
    protected $typeProductContent;

    public function __construct($typeProduct, $typeProductContent)
    {
        $this->typeProduct = $typeProduct;
        $this->typeProductContent = $typeProductContent;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if($this->typeProduct == 'I' && $this->typeProductContent == 'E'){
            if(!Product::where('id',$value)->whereDoesntHave('childrenProducts')->exists()){
                $fail('Uno de los subproductos no cumple con la parametrización');
            }
            return;
        }else{
            $fail('El tipo de producto no es apto para contener subproductos');
        }

    }
}
