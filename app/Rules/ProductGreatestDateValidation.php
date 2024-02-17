<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductGreatestDateValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $startDate;

    public function __construct($startDate)
    {
        $this->startDate = $startDate;
    }


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (strtotime($value) > strtotime($this->startDate)){
            $fail("La fecha de finalización debe ser mayor a la fecha de inicio del evento.");
        }
    }
}
