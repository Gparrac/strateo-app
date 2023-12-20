<?php

namespace App\Http\Controllers\CRUD\EnterpriseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Third;
use App\Models\Company;
use App\Models\User;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $company = Company::first();

            // Find Third with third_id in company
            $third = Third::findOrFail($company->third_id);
            // Create a record in the Third table
            $third->fill($request->only([
                'type_document',
                'identificacion',
                'verification_id',
                'names',
                'surnames',
                'business_name',
                'address',
                'mobile',
                'email',
                'email2',
                'postal_code',
                'city_id',
            ]) + ['users_update_id' => $user->id])->save();

            //Since the path_logo attribute has a CAST, the data must be manually assigned if it exists
            if($request->hasFile('path_logo')){
                $company->path_logo = $request->file('path_logo')->store('logos');
            }

            $company->fill($request->only([
                'header',
                'footer',
            ]))->save();

            // Commit the transaction
            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('Query error CompanyParameterization@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('unknown error CompanyParameterization@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}