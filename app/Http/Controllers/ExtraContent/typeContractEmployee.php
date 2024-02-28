<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class typeContractEmployee extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $types =[
           ['name' => 'Termino fijo', 'id' => 'TF'],
           ['name' => 'Termino indefinido', 'id' => 'TI'],
           ['name' => 'Obra o labor', 'id' => 'OL'],
           ['name' => 'Prestación de servicios', 'id' => 'PS'],
           ['name' => 'Contrato de aprendizaje', 'id' => 'CA'],
           ['name' => 'Ocasional de trabajo', 'id' => 'OT']
        ];
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
