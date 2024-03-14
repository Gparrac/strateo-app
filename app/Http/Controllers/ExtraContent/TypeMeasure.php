<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeMeasure extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $types = [
            ['name' => 'Tiempo',  'id' => 'TI'],
            ['name' => 'Tamaño',  'id' => 'LE'],
            ['name' => 'Peso',  'id' => 'WE'],
            ['name' => 'Volumen',  'id' => 'VO'],
        ];
        //TI: TIME, LE:LENGHT, WE:WEIGHT, VO: VOLUME
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
