<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeProduct extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $types = [];
        if ($request->has('attribute')) {
            if ($request['attribute'] == 'type') {
                $types = [
                    ['name' => 'Servicio',  'id' => 'SE'],
                    ['name' => 'Producto',  'id' => 'PR'],
                    ['name' => 'Lugar',  'id' => 'PL'],
                ];
            } else {
                $types = [
                    ['name' => 'Reutilizable',  'id' => 'R'], ['name' => 'Consumible',  'id' => 'C']
                ];
            }
        }
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
