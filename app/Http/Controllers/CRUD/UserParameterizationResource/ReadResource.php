<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('user_id')) {
            return $this->singleRecord($request->input('user_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'));
        }
    }

    public function singleRecord($id)
    {
        $data = User::with(['third' => function ($query) {
            $query->with('city:id,name');
        }, 'role:id,name', 'offices:id,name'])->find($id);
        return response()->json(['message' => 'Read: ' . $id, 'data' => $data], 200);
    }
    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null)
    {
        try {

            $data = User::with(['third' => function ($query) {
                // $query->select('id','names','city_id'); //for specify fields and define the richment 🕐
                $query->with('city:id,name');
            }, 'role:id,name', 'offices:id,name']);
            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
            }
            //append shorters to query
            foreach ($sorters as $key => $shorter) {
                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
            return response()->json(['message' => 'Read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
