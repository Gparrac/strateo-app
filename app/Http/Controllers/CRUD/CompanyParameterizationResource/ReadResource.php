<?php

namespace App\Http\Controllers\CRUD\CompanyParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class ReadResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $user = Auth::user();
            $third = $request->third;

            $thirdResponse = [
                'type_document' => $third->type_document,
                'identificacion' => $third->identificacion,
                'verification_id' => $third->verification_id,
                'names' => $third->names,
                'surnames' => $third->surnames,
                'business_name' => $third->business_name,
                'address' => $third->address,
                'mobile' => $third->mobile,
                'email' => $third->email,
                'postal_code' => $third->postal_code,
                'city_id' => $third->city_id,
            ];

            $company = Company::select('path_logo', 'header', 'footer')
                        ->where('third_id', $third->id)
                        ->first();
            
            $companyArray = $companyInfo ? $companyInfo->toArray() : [];

            return response()->json(array_merge($thirdResponse, $company), 200);

        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}