<?php

namespace App\Http\Controllers\CRUD\SupplierParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            // Client update
            $service = Service::findOrFail($request->input('service_id'));
            //Save the new files

            $service->fill($request->only([
                'name',
                'description',
                'status',
            ])+ ['users_update_id' => $userId])->save();
            DB::table('field_services')->where('services_id',$service['id'])->update(['status'=> 'I']);
            foreach ($request['fields_id'] as $value) {
                $query = DB::table('field_services')->where('services_id',$service['id'])->where('fields_id',$value['id']);
                if ($query->count() == 0) {

                    $service->permissions()->attach($value['id'], [
                        'status' => 'A',
                        'required' => $value['required'],
                        'users_id' => $userId
                    ]);
                } else {
                    $query->update([
                        'status' => 'A',
                        'required' => $value['required'],
                        'users_update_id' => $userId
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error ClientResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error ClientResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
