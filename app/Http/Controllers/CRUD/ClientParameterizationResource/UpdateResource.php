<?php

namespace App\Http\Controllers\CRUD\ClientParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Third;
use App\Models\Client;
use App\Models\User;
use App\Http\Utils\FileFormat;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            // Client update
            $client = Client::findOrFail($request->input('client_id'));
            //Save the new files
            if($request->hasFile('commercial_registry_file')){
                $client->commercial_registry_file = $request->file('commercial_registry_file')
                ->storeAs(
                    'commercial', 
                    FileFormat::formatName($request->file('commercial_registry_file')->getClientOriginalName(),
                    $request->file('commercial_registry_file')->guessExtension()));
            }
            if($request->hasFile('rut_file')){
                $client->rut_file = $request->file('rut_file')
                ->storeAs(
                    'rut',
                    FileFormat::formatName($request->file('rut_file')->getClientOriginalName(),
                    $request->file('rut_file')->guessExtension()));
            }

            $client->fill($request->only([
                'commercial_registry',
                'legal_representative_name',
                'legal_representative_id',
                'note',
                'status',
            ])+ ['users_update_id' => $user->id])->save();
            
            $third = Third::findOrFail($client->third_id);
            //third update
            $third->fill($request->only([
                'type_document',
                'identificacion',
                'names',
                'surnames',
                'address',
                'mobile',
                'email',
                'email2',
                'postal_code',
                'city_id',
            ]) + ['users_update_id' => $user->id])->save();

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