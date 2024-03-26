<?php

namespace App\Http\Controllers\CRUD\EnterpriseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Third;
use App\Models\Company;
use App\Http\Utils\CastVerificationNit;
class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id() ?? 2;
            // Create body to create third record
            $thirdData = [
                'type_document' => $request->input('type_document'),
                'identification' => $request->input('identification'),
                'verification_id' =>  CastVerificationNit::calculate($request['identification']),
                'names' => $request->input('names') ?? null,
                'surnames' => $request->input('surnames') ?? null,
                'business_name' => $request->input('business_name') ?? null,
                'address' => $request->input('address'),
                'mobile' => $request->input('mobile'),
                'email' => $request->input('email'),
                'postal_code' => $request->input('postal_code') ?? null,
                'city_id' => $request->input('city_id'),
                'users_id' => $userId,
                'code_ciiu_id' => $request->input('code_ciiu_id')
            ];

            // Check if 'email2' is present in the request before adding it to the array
            if ($request->has('email2')) {
                $thirdData['email2'] = $request->input('email2');
            }
            // Create a record in the Third table
            $third = Third::create($thirdData);

            if($request->has('secondary_ciiu_ids')){
                $third->secondaryCiius()->attach($request['secondary_ciiu_ids'],[
                    'status' => 'A',
                    'users_id' => $userId,
                    'users_update_id' => $userId,
                ]);
            }
            // Create a record in the Company table
            $company = Company::create([
                'path_logo' => $request->file('path_logo')->store('logos'),
                'header' => $request->input('header'),
                'footer' => $request->input('footer'),
                'third_id' => $third->id
            ]);

            // Commit the transaction
            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('Query error CompanyParameterization@createResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('unknown error CompanyParameterization@createResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
