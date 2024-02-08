<?php

namespace App\Http\Controllers\CRUD\EnterpriseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('query_id')){
            return $this->singleRecord($request->input('query_id'));
        }else{
            return $this->allRecords();
        }
    }

    public function singleRecord($id){
        return response()->json(['message' => 'Delete: '. $id], 200);
    }

    public function allRecords($ids = null){
        return response()->json(['message' => 'Delete'], 200);
    }
}