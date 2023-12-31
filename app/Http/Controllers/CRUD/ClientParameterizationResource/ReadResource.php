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
        if($request->has('client_id')){
            return $this->singleRecord($request->input('client_id'));
        }else{
            return $this->allRecords();
        }
    }

    public function singleRecord($id){
        try {
            $client = Client::with(['third' => function ($query){
                $query->select('id','type_document','identification','code_ciiu_id','verification_id','names','surnames','business_name','address','mobile','email','email2','postal_code','city_id');
                $query->with('ciiu:id,code,description');
            }])
                ->where('clients.id', $id)
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
        $clients = Client::select('id','status', 'legal_representative_name', 'legal_representative_id', 'third_id')
                ->with('third:id,identification,email')
                ->paginate(20);

        return response()->json(['message' => 'read', 'data' => $clients], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
