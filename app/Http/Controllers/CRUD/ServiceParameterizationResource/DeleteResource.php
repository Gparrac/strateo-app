<?php

namespace App\Http\Controllers\CRUD\ServiceParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('client_id')){
            return $this->singleRecord($request->input('client_id'));
        }else{
            return $this->allRecords($request->input('client_ids'));
        }
    }

    public function singleRecord($id){
        try {
            $userId = auth()->id();
            $service = Service::where('id', $id)->firstOrFail();
            // Create a record in the Office table
            $service->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '. $id], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientParameterization@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientParameterization@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }

    public function allRecords($ids = null,$pagination=5, $sorters = [], $keyword =null, $typeKeyword = null){
        try {
            $idsArray = json_decode($ids, true);
            $userId = auth()->id();

            Service::whereIn('id', $idsArray)->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '.$ids], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientParameterization@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientParameterization@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }
}
