<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeFieldServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $types =[
            ['name' => 'Archivo', 'icon' => 'mdi-file-send', 'id' => 'F'],
            ['name' => 'Texto', 'icon' => 'mdi-format-font', 'id' => 'T'],
            ['name' => 'Número', 'icon' => 'mdi-numeric', 'id' => 'N'],
            ['name' => 'Alfanumerico', 'icon' => 'mdi-format-header-pound', 'id' => 'A'],
        ];
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
