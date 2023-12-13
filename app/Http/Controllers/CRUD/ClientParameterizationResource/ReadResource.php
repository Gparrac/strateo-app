<?php

namespace App\Http\Controllers\CRUD\ClientParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Third;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('client_id')){
            return $this->singleRecord($request->input('client_id'));
        }else{
            return $this->allRecords();
        }
    }

    public function singleRecord($id){
        try {
            $client = Third::select('id','type_document', 'identification', 'verification_id',
                'names', 'surnames', 'address', 'mobile', 'email', 'postal_code', 'city_id')
                ->where('id', $id)
                ->first();

            return response()->json(['message' => 'read: '.$id, 'data' => $client], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null){
        try {
        $clients = Third::paginate(20);

        return response()->json(['message' => 'read: '.$id, 'data' => $clients], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}