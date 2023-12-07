<?php

namespace App\Http\Controllers\CRUD\CompanyParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;

class ReadResource implements CRUD, RecordOperations
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
        return response()->json(['message' => 'Read: '.$id], 200);
    }
    public function allRecords(){
        return response()->json(['message' => 'Read'], 200);
    }
}