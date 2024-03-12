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
                    ['name' => 'Intangible',  'id' => 'I'],
                    ['name' => 'Tangible',  'id' => 'T'],

                ];
            } else {
                if($request->has('type')){
                    $types = $request->input('type') == 'T' ?
                    [['name' => 'Consumible',  'id' => 'C'], ['name'=> 'Reutilizable', 'id'=> 'R']]
                    : [['name' => 'Evento',  'id' => 'E'],['name' => 'Lugar',  'id' => 'L']];
                }else{
                    $types = [
                        ['name' => 'Evento',  'id' => 'E'],['name' => 'Lugar',  'id' => 'L'], ['name' => 'Consumible',  'id' => 'C'], ['name'=> 'Reutilizable', 'id'=> 'R']
                    ];
                }
            }
        }
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
