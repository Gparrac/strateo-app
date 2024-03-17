<?php

namespace App\Http\Utils;

class PriceFormat
{
    public static function getNumber($number)
    {
        // Separate the integer part and the decimal part
        $parts = explode('.', $number);

        // Format the integer part with apostrophes for millions and dots for thousands
        $formattedIntegerPart = number_format($parts[0], 0, '', ".");
        
        // If there is a decimal part, append it to the formatted number
        $formattedNumber = isset($parts[1]) ? $formattedIntegerPart . ',' . $parts[1] : $formattedIntegerPart;

        return $formattedNumber;
    }
}