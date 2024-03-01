<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Third;
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
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'), $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        $data = User::with(['third' => function ($query) {
            $query->with('city:id,name');
        }, 'role:id,name', 'offices:id,name'])->find($id);
        return response()->json(['message' => 'Read: ' . $id, 'data' => $data], 200);
    }
    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null, $format = null)
    {
        try {


            $data = new User();
            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                if ($typeKeyword == 'third') {
                    $data = $data->whereHas('third', function ($query) use ($typeKeyword, $keyword) {
                        $query->where('identification', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('names', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('surnames', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('business_name', 'LIKE', '%' . $keyword . '%');
                    });
                } else {

                    $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
                }
            }

            if ($format == 'short') {
                $data = $data

                    ->select('users.id', 'users.name', 'third_id')->take(10)->get()
                    ->map(function ($query) {
                        $shortForm = [
                            'id' => $query->id,
                            'name' => $query->name,
                            'full_name' => $query['third']['names'],
                            'identification' => $query['third']['type_document'] . '. ' . $query['third']['identification']
                        ];
                        unset($query['third_id']);
                        unset($query['third']);
                        return $shortForm;
                    });
            } else {
                $data = $data->with(['third' => function ($query) {
                    $query->with('city:id,name');
                }, 'role:id,name', 'offices:id,name']);

                //append shorters to query
                foreach ($sorters as $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }
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
