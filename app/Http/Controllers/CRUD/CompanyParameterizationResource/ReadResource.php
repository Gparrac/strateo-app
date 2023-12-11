<?php

namespace App\Http\Controllers\CRUD\CompanyParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Models\Third;

class ReadResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $user = Auth::user() ?? User::find(2);
            // Find Third with third_id in user
            $third = Third::select('id','type_document', 'identification', 'verification_id',
                'business_name', 'address', 'mobile', 'email', 'postal_code', 'city_id')
                ->where('id', $user->third_id)
                ->first();

            $company = Company::select('path_logo', 'header', 'footer')
                        ->where('third_id', $third->id)
                        ->first();
            $companyArray = $company ? $company->toArray() : [];

            return response()->json(array_merge($third->toArray(), $companyArray), 200);

        } catch (QueryException $ex) {
            Log::error('Query error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error CompanyParameterization@readResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}