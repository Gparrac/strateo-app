<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\Third;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateResource implements CRUD
{

    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $authId = Auth::id() ?? 1;
            // $authId = Auth::id();
            $newThird = Third::create([
                'type_document' => $request['type_document'],
                'identification' => $request['identification'],
                'business_name' => $request['business_name'] ?? null,
                'names' => $request['names'] ?? null,
                'surnames' => $request['surnames'] ?? null,
                'verification_id' => $request['verification_id'] ?? null,
                'address' => $request['address'],
                'mobile' => $request['mobile'],
                'email' => $request['email'],
                'email2' => $request['email2'],
                'city_id' => $request['city_id'],
                'users_update_id' => $authId, // meanwhile define auth module ⚠️
                'users_id' => $authId
            ]);

            $newUser = User::create([
                'name' => $request['name'],
                'password' => bcrypt($request['password']),
                'third_id'=> $newThird['id'],
                'role_id' => $request['role_id'],
                'users_id' => $authId,
                'users_update_id' => $authId, // meanwhile define auth module ⚠️
                'status' => $request['status']
            ]);
            $newUser->offices()->attach($request['offices_id'],[
                'status'=>'A',
                'users_id'=>$authId
            ]);
            DB::commit();
            return response()->json(['message' => 'Create'], 200);
        } catch (QueryException $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('Query error UsersParameterization@createResource: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('unknown error UsersParameterization@createResource: ' . $ex->getMessage());
            return response()->json(['error' => 'create u'], 500);
        }
    }
}
