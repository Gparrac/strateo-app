<?php

namespace App\Http\Controllers\CRUD\ClientParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('client_id')) {
            return $this->singleRecord($request->input('client_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('typeKeyword'), $request->input('keyword'),$request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Client::with(['third' => function ($query) {

                    $query->with(['city:id,name','ciiu:id,code,description']);
                $query->select('id', 'type_document', 'identification', 'code_ciiu_id', 'verification_id', 'names', 'surnames', 'business_name', 'address', 'mobile', 'email', 'email2', 'postal_code', 'city_id');
            }])
                ->where('clients.id', $id)
                ->first();

            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $typeKeyword = null, $keyword = null, $format = null)
    {
        try {
            $data = new Client();
            //filter query with keyword 🚨
            if ($typeKeyword && $keyword) {
                if($typeKeyword == 'legal_credencials'){
                    $data = $data->where('legal_representative_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('legal_representative_id','LIKE', '%' . $keyword . '%');
                }else{

                    $data = $data->where($typeKeyword, 'LIKE', '%' . $keyword . '%');
                }
            }
            if($format == 'short'){
                $data = $data->where('status','A')->select('clients.id', 'legal_representative_name as name','legal_representative_id as document')->take(10)->get();
            }else{
                $data = $data->select('id', 'status', 'legal_representative_name', 'legal_representative_id', 'third_id', 'updated_at')
                ->with('third:id,identification,email');
            //append shorters to query
            foreach ($sorters as $shorter) {

                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
            }

            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
