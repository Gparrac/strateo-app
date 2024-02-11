<?php

namespace App\Rules;

use App\Models\Service;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ServiceFieldSizeValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sindex = explode('.', $attribute)[1];
        $service_id = request()->input("services.{$sindex}.service_id");
        $fields = request()->input("services.{$sindex}.fields");
        if (count($fields['fields']) != Service::find($service_id)->fields()->count()) {
            $fail('La cantidad de campos no coincide con el servicio seleccionado.');
        }
    }
}
