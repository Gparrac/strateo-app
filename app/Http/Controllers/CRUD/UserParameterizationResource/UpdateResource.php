<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $authId = Auth::id();
            User::find($request['user_id'])->third->update([
                'type_document' => $request['type_document'],
                'identification' => $request['identification'],
                'names' => $request['names'],
                'surnames' => $request['surnames'],
                'address' => $request['address'],
                'mobile' => $request['mobile'],
                'email' => $request['email'],
                'email2' => $request['email2'],
                'city_id' => $request['city_id'],
                'users_update_id' => $authId // meanwhile define auth module ⚠️
            ]);

            DB::table('office_users')->where('office_users_id',$request['user_id'])->update([
                'status' => 'I',
                'users_update_id' => $authId, // meanwhile define auth module ⚠️
            ]);

            foreach ($request['offices_id'] as $key => $officeId) {
                $query = DB::table('office_users')
                ->where('office_users_id',$request['user_id'])
                ->where('office_id',$officeId);


                if($query->count() == 0){
                    User::find($request['user_id'])->offices()->attach($officeId,[
                        'status'=>'A',
                        'users_id'=>$authId,
                        'users_update_id' => $authId // meanwhile define auth module ⚠️
                    ]);
                }else{


                    $query->update([
                        'status'=>'A',
                        'users_update_id' => $authId, // meanwhile define auth module ⚠️
                    ]);

                }
            }

            User::find($request['user_id'])->update([
                'name' => $request['name'],
                'password' => bcrypt($request['password']),
                'role_id' => $request['role_id'],
                'users_update_id' => $authId, // meanwhile define auth module ⚠️
                'status' => $request['status']
            ]);
            DB::commit();
            return response()->json(['message' => 'Update'], 200);
        } catch (QueryException $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('Query error UsersParameterization@createResource: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            // In case of error, roll back the transaction
            DB::rollback();
            Log::error('unknown error UsersParameterization@createResource: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
