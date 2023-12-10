<?php

namespace App\Http\Controllers\CRUD\CompanyParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Third;
use App\Models\Company;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            // Find Third with third_id in user
            $third = Third::find($request->input($user->third_id));

            // Create body to create third record
            $thirdData = [
                'type_document' => $request->input('type_document'),
                'identificacion' => $request->input('identificacion'),
                'verification_id' => $request->input('verification_id'),
                'names' => $request->input('names'),
                'surnames' => $request->input('surnames'),
                'business_name' => $request->input('business_name'),
                'address' => $request->input('address'),
                'mobile' => $request->input('mobile'),
                'email' => $request->input('email'),
                'postal_code' => $request->input('postal_code'),
                'city_id' => $request->input('city_id'),
                'users_update_id' => $user->id
            ];

            // Check if 'email2' is present in the request before adding it to the array
            if ($request->has('email2')) {
                $thirdData['email2'] = $request->input('email2');
            }

            // Create a record in the Third table
            $third = $third->update($thirdData);

            // Update the existing Company record
            $company = Company::where('third_id', $third->id)->first();
            $company->update([
                'path_logo' => $request->file('path_logo')->store('logos'),
                'header' => $request->input('header'),
                'footer' => $request->input('footer'),
            ]);

            // Commit the transaction
            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('Query error CompanyParameterization@updateResource: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('unknown error CompanyParameterization@updateResource: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}