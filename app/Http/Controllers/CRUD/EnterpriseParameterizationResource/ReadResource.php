<?php

namespace App\Http\Controllers\CRUD\EnterpriseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Database\QueryException;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
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
        if($request->has('enterprise_id')){
            return $this->singleRecord($request->input('enterprise_id'));
        }else{
            return $this->allRecords();
        }
    }

    public function singleRecord($id){
        try {
            $third = Third::select('id', 'type_document', 'identification', 'verification_id',
            'business_name', 'address', 'mobile', 'email', 'email2', 'postal_code', 'city_id')
            ->findOrFail($id);

            $company = Company::select('path_logo', 'header', 'footer')
                        ->where('third_id', $third->id)
                        ->first();
            $companyArray = $company ? $company->toArray() : [];
            $data = array_merge($third->toArray(), $companyArray);

            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error EnterpriseParameterization@ReadResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error EnterpriseParameterization@ReadResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null){
        try {
            $user = Auth::user();
            // Find Third with third_id in user
            $third = Third::select('id', 'type_document', 'identification', 'verification_id',
                'business_name', 'address')
                ->whereNotNull('business_name')
                ->paginate(10);

            return response()->json(['message' => 'read', 'data' => $third], 200);
        } catch (QueryException $ex) {
            Log::error('Query error EnterpriseParameterization@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error EnterpriseParameterization@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}