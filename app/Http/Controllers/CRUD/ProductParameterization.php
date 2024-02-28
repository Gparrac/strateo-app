<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRUD\ProductParameterizationResource\ConnectResource;
use App\Http\Controllers\CRUD\ProductParameterizationResource\CreateResource;
use App\Http\Controllers\CRUD\ProductParameterizationResource\ReadResource;
use App\Http\Controllers\CRUD\ProductParameterizationResource\UpdateResource;
use App\Http\Controllers\CRUD\ProductParameterizationResource\DeleteResource;

class ProductParameterization extends Controller
{
    /**
     * Handle the incoming request.
     * $request Query = query_id (int)
     */
    public function __invoke(Request $request)
    {
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
