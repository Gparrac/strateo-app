<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Third;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('user_id')){
            return $this->singleRecord($request->input('user_id'));
        }else{
            $pagination = $request->has('pagination') ? $request->input('pagination') : 10;
            $sorters = $request->has('sorters') ? $request->input('sorters') : [];
            $typeKeyword = $request->has('typeKeyword') ? $request->input('typeKeyword') : null;
            $keyword = $request->has('keyword') ? $request->input('keyword') : null;
            return $this->allRecords(null, $pagination, $sorters, $typeKeyword, $keyword );
        }
    }

    public function singleRecord($id){
        $data = User::with(['third' => function ($query){
            $query->with('city:id,name');
        },'role:id,name','offices:id,name'])->find($id);
        return response()->json(['message' => 'Read: '.$id, 'data' => $data], 200);
    }
    public function allRecords($ids=null, $pagination, $sorters = [], $typeKeyword, $keyword){
        $data = User::with(['third' => function ($query){
            // $query->select('id','names','city_id'); //for specify fields and define the richment 🕐
            $query->with('city:id,name');
        },'role:id,name','offices:id,name']);
        Log::info($typeKeyword);
        //filter query with keyword
        if($typeKeyword && $keyword){
            $data = $data->where($typeKeyword,$keyword);
            // $data = $data->where($typeKeyword,);
        }
        //append shorters to query
        foreach ($sorters as $key => $shorter) {
            $data = $data->orderBy($shorter['key'], $shorter['order']);
        }
        $data = $data->paginate($pagination); // should be with paginate() but i've still known how to consume in front ⌚
        return response()->json(['message' => 'Read', 'data' => $data], 200);
    }
}
