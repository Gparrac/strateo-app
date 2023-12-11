<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Third;
use App\Models\User;
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
        $data = User::with(['third' => function ($query){
            $query->with('city:id,name');
        },'role:id,name'])->find($id);
        return response()->json(['message' => 'Read: '.$id, 'data' => $data], 200);
    }
    public function allRecords(){
        $data = User::with(['third' => function ($query){
            // $query->select('id','names','city_id'); //for specify fields and define the richment 🕐
            $query->with('city:id,name');
        },'role:id,name','offices:id,name'])->get(); // should be with paginate() but i've still known how to consume in front ⌚
        return response()->json(['message' => 'Read', 'data' => $data], 200);
    }
}
