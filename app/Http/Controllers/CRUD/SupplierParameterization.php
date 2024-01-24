<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRUD\SupplierParameterizationResource\CreateResource;
use App\Http\Controllers\CRUD\SupplierParameterizationResource\ReadResource;
use App\Http\Controllers\CRUD\SupplierParameterizationResource\UpdateResource;
use App\Http\Controllers\CRUD\SupplierParameterizationResource\DeleteResource;
use Illuminate\Support\Facades\Log;

class SupplierParameterization extends Controller
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
            case 'DELETE':
                $strategy = new CRUDContext(new DeleteResource());
                break;
            default:
                return response()->json(['error' => 'Method not allowed']);
        }

        $execResource = $strategy->execResource($request);

        return $execResource;
    }
}
