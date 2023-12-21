<?php

namespace App\Http\Controllers\CRUD\ClientParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Third;
use App\Models\Client;
use App\Http\Utils\FileFormat;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            // Create body to create third record
            $thirdData = [
                'type_document' => $request->input('type_document'),
                'identification' => $request->input('identification'),
                'verification_id' => $request->input('verification_id'),
                'names' => $request->input('names') ?? null,
                'surnames' => $request->input('surnames') ?? null,
                'business_name' => $request->input('business_name') ?? null,
                'address' => $request->input('address'),
                'mobile' => $request->input('mobile'),
                'email' => $request->input('email'),
                'postal_code' => $request->input('postal_code'),
                'city_id' => $request->input('city_id'),
                'users_id' => $userId
            ];

            // Check if 'email2' is present in the request before adding it to the array
            if ($request->has('email2')) {
                $thirdData['email2'] = $request->input('email2');
            }

            // Create a record in the Third table
            $third = Third::create($thirdData);

            $client = Client::create([
                'commercial_registry' => $request->input('commercial_registry'),
                'commercial_registry_file' => $request->file('commercial_registry_file')
                        ->storeAs(
                            'commercial', 
                            FileFormat::formatName($request->file('commercial_registry_file')->getClientOriginalName(),
                            $request->file('commercial_registry_file')->guessExtension())),
                'rut_file' => $request->file('rut_file')
                        ->storeAs(
                            'rut', 
                            FileFormat::formatName($request->file('rut_file')->getClientOriginalName(),
                            $request->file('rut_file')->guessExtension())),
                'legal_representative_name' => $request->input('legal_representative_name'),
                'note' => $request->input('note'),
                'status' => $request->input('status'),
                'third_id' => $third->id,
                'users_id' => $userId
            ]);

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error ClientResource@createResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error ClientResource@createResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}