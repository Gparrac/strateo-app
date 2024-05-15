<?php

namespace App\Http\Controllers\CRUD\OfficeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Office;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('office_id')){
            return $this->singleRecord($request->input('office_id'));
        }else{
            return $this->allRecords($request->input('office_ids'));
        }
    }

    public function singleRecord($id){
        try {
            $userId = auth()->id();
            $office = Office::where('id', $id)->firstOrFail();
            // Create a record in the Office table
            $office->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '. $id], 200);
        } catch (QueryException $ex) {
            Log::error('Query error OfficeParameterization@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error OfficeParameterization@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }

    public function allRecords($ids = null,  $pagination=5, $sorters = [], $filters=[], $format = null){
        try {
            $idsArray = json_decode($ids, true);
            $userId = auth()->id();

            Office::whereIn('id', $idsArray)->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '.$ids], 200);
        } catch (QueryException $ex) {
            Log::error('Query error OfficeParameterization@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error OfficeParameterization@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }
}
