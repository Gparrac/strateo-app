<?php

namespace App\Http\Controllers\CRUD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRUD\BrandParameterizationResource\CreateResource;
use App\Http\Controllers\CRUD\BrandParameterizationResource\ReadResource;
use App\Http\Controllers\CRUD\BrandParameterizationResource\UpdateResource;
use App\Http\Controllers\CRUD\BrandParameterizationResource\DeleteResource;

class BrandParameterization extends Controller
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
