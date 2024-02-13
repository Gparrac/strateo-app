<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypedocumentUserServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if($request->input("type") == "person"){
            $typeDocuments = [
                ['name' => 'CC', 'label' => 'Cedula de ciudadania'],
                ['name' => 'CE', 'label' => 'Cedula de extranjeria'],
                ['name' => 'PASAPORTE', 'label' => 'Pasaporte']
            ];
        }else{
            $typeDocuments = [
                ['name' => 'CC', 'label' => 'Cedula de ciudadania'],
                ['name' => 'CE', 'label' => 'Cedula de extranjeria'],
                ['name' => 'PASAPORTE', 'label' => 'Pasaporte'],
                ['name' => 'NIT', 'label' => 'Número de Identificación Tributaria'],
            ];
        }
        return response()->json(['message' => 'Read: ', 'data' => $typeDocuments], 200);
    }
}
