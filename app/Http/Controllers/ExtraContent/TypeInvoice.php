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
        if (request()->has("planment_stage")) {
            switch (request()->input('planment_stage')) {
                case 'QUO':
                    $types = [
                        ['name' => 'Cotización',  'id' => 'QUO'],
                        ['name' => 'Listo',  'id' => 'REA'],
                        ['name' => 'Cancelado',  'id' => 'CAN']
                    ];
                    break;
                case 'REA':
                    $types = [
                        ['name' => 'Cotización',  'id' => 'QUO'],
                        ['name' => 'Listo',  'id' => 'REA'],
                        ['name' => 'Finalizado',  'id' => 'FIN'],
                        ['name' => 'Cancelado',  'id' => 'CAN']
                    ];
                    break;
                case 'FIN':
                    $types = [
                        ['name' => 'Cotización',  'id' => 'QUO'],
                        ['name' => 'Listo',  'id' => 'REA'],
                        ['name' => 'Finalizado',  'id' => 'FIN'],
                        ['name' => 'Cancelado',  'id' => 'CAN']
                    ];
                    break;
                case 'CAN':
                    $types = [
                        ['name' => 'Cotización',  'id' => 'QUO'],
                        ['name' => 'Listo',  'id' => 'REA'],
                        ['name' => 'Cancelado',  'id' => 'CAN']
                    ];
                    break;
                default:
                $types = [
                    ['name' => 'Cotización',  'id' => 'QUO']
                ];
                    break;
            }
        } else {
            $types = [
                ['name' => 'Venta directa',  'id' => 'P'],
                ['name' => 'Planeación',  'id' => 'E'],
            ];
        }
        return response()->json(['message' => 'Read: ', 'data' => $types], 200);
    }
}
