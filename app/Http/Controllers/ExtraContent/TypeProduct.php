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
                    ['name' => 'Tangible',  'id' => 'I'],
                    ['name' => 'Intangible',  'id' => 'T'],

                ];
            } else {
                $types = [
                    ['name' => 'Evento',  'id' => 'E'], ['name' => 'Consumible',  'id' => 'C'],['name' => 'Lugar',  'id' => 'L']
                ];
            }
        }
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
