<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRUD\InventoryParameterizationResource\CreateResource;
use App\Http\Controllers\CRUD\InventoryParameterizationResource\ReadResource;
use App\Http\Controllers\CRUD\InventoryParameterizationResource\UpdateResource;
use App\Http\Controllers\CRUD\InventoryParameterizationResource\DeleteResource;
use Illuminate\Support\Facades\Log;

class InventoryParameterization extends Controller
{
    /**
     * Handle the incoming request.
     * $request Query = query_id (int)
     */
    public function __invoke(Request $request)
    {
        Log::info('mensage');
        switch($request->method()){
            case 'POST':
                $strategy = new CRUDContext(new CreateResource());
                break;
            case 'GET':
                $strategy = new CRUDContext(new ReadResource());
                break;
            case 'PUT':
                $strategy = new CRUDContext(new UpdateResource());
                break;
            default:
                return response()->json(['error' => 'Method not allowed']);
        }

        $execResource = $strategy->execResource($request);

        return $execResource;
    }
}
