<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeInventoryTrade extends Controller
{
    public function __invoke(Request $request)
    {
        $types = [];
        if($request->has('attribute')){
            if($request['attribute'] == 'type'){
                $types =[
                    ['name' => 'Entrada', 'icon' => 'mdi-login', 'id' => 'E'],
                    ['name' => 'Salida', 'icon' => 'mdi-logout', 'id' => 'D']
                ];
            }else{
                if($request->has('type')){
                    if($request['type'] == 'E'){
                        $types = [['name' => 'Balance inicial',  'id' => 'IB'],
                        ['name' => 'Donación',  'id' => 'D'],
                        ['name' => 'Ajuste', 'id' => 'A']];
                    }else{
                        $types = [['name' => 'Ventas', 'id' => 'S'], ['name' => 'Ajuste', 'id' => 'A']];
                    }
                }else{
                    $types = [];
                }

            }
        }
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
