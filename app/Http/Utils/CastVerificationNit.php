<?php

namespace App\Http\Utils;


class CastVerificationNit
{
    public static function calculate($strNit) {
        if ($strNit !== "") {
            $strNit = strval($strNit);
            // Vector de números primos
            $iNrosPrimos = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
            // Variable para realizar las operaciones
            $iOperacion = 0;

            // Ciclo para multiplicar cada uno de los dígitos del NIT con el vector
            for ($i = 0; $i < strlen($strNit); $i++) {
                $iOperacion += intval(substr($strNit, strlen($strNit) - ($i + 1), 1)) * $iNrosPrimos[$i];
            }

            // Sacar el residuo de la operación
            $iOperacion %= 11;

            if ($iOperacion === 0 || $iOperacion === 1) {
                return $iOperacion;
            } else {
                return 11 - $iOperacion;
            }
        } else {
            return null;
        }
    }
}
