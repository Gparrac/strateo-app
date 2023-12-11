<?php

namespace App\Http\Controllers\CRUD\RoleParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Role;
use App\Models\Third;
use App\Models\User;
use Illuminate\Http\Request;

class ReadResource implements CRUD, RecordOperations
{

    private $format;
    public function resource(Request $request)
    {
        if($request->has('query_id')){
            return $this->singleRecord($request->input('query_id'));
        }else{
            $this->format = $request->input('format');
                return $this->allRecords();
        }
    }

    public function singleRecord($id){
        $data = Role::with('permissions')->find($id);
        return response()->json(['message' => 'Read: '.$id, 'data' => $data], 200);
    }
    public function allRecords(){
        if($this->format == 'short'){
            $data = Role::select('id','name')->get();
        }else{
            $data = Role::with('permissions')->get();
        }

        return response()->json(['message' => 'Read', 'data' => $data], 200);
    }

}
