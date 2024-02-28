<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeInvoice extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        if(request()->has("planmentInvoice")){
            $types =[
                ['name' => 'Venta directa',  'id' => 'P'],
                ['name' => 'Planeación',  'id' => 'E'],
            ];
        }else{
            $types = [
                ['name' => 'Cotización',  'id' => 'QUO'],
                ['name' => 'Confirmación',  'id' => 'CON'],
                ['name' => 'Listo',  'id' => 'REA'],
                ['name' => 'Finalizado',  'id' => 'FIN'],
                ['name' => 'Cancelado',  'id' => 'CAN']
            ];
        }
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
