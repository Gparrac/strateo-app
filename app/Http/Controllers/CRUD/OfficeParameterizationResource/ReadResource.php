<?php

namespace App\Http\Controllers\CRUD\OfficeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Office;

class ReadResource implements CRUD, RecordOperations
{
    private $format;
    public function resource(Request $request)
    {
        if($request->has('office_id')){
            return $this->singleRecord($request->input('office_id'));
        }else{
            $this->format = $request->input('format');
            return $this->allRecords();
        }
    }

    public function singleRecord($id)
    {
        try {
            $office = Office::where('id', $id)
                    ->firstOrFail(['name', 'address', 'phone', 'city_id', 'status']);
            // Create a record in the Office table
            return response()->json(['message' => 'read: '.$id, 'data' => $office], 200);
        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null)
    {
        try {
            if($this->format == 'short'){
                $office = Office::select('id','name')->get();
            }else{
                $office = Office::paginate(20);
            }

            return response()->json(['message' => 'Read', 'data' => $office], 200);
        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
