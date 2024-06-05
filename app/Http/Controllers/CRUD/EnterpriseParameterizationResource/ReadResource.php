<?php

namespace App\Http\Controllers\CRUD\EnterpriseParameterizationResource;

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
            $company = Company::with(['googleUser:id,name,email'])->firstOrFail(['path_logo', 'header', 'footer', 'third_id','google_user_id']);
            $third = Third::select('id', 'type_document', 'identification', 'verification_id',
            'names', 'surnames', 'business_name', 'address', 'mobile', 'email', 'email2',
            'postal_code', 'city_id', 'code_ciiu_id')
            ->with('ciiu:id,code,description')
            ->with(['secondaryCiius' => function($query){
                $query->where('status','A');
            }])
            ->findOrFail($company->third_id);
            $third->secondaryCiius->map(function ($item) {
                unset($item->pivot);
                return $item;
            });
            $data = array_merge($third->toArray(), $company->toArray());

            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error EnterpriseParameterization@ReadResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error EnterpriseParameterization@ReadResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
