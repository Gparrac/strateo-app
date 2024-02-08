<?php

namespace App\Http\Controllers\CRUD\WarehouseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;
use App\Models\Third;
use Illuminate\Support\Facades\Auth;
use App\Http\Utils\CastVerificationNit;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

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
                'postal_code' => $request->input('postal_code'),
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
            
            Warehouse::create([
                'note' => $request->input('note'),
                'third_id' => $third->id,
                'city_id' => $request->input('city_id'),
                'address' => $request->input('address_warehouse'),
                'status' => $request->input('status'),
                'users_id' => $userId,
            ]);

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
