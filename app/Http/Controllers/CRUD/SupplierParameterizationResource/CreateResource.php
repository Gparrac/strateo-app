<?php

namespace App\Http\Controllers\CRUD\SupplierParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Utils\CastVerificationNit;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Third;
use App\Models\Client;
use App\Http\Utils\FileFormat;
use App\Models\DynamicService;
use App\Models\Service;
use App\Models\Supplier;

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
                'verification_id' => $request['type_document'] == 'NIT' ? CastVerificationNit::calculate($request['identification']) : NULL ,
                'names' => $request->input('names') ?? null,
                'surnames' => $request->input('surnames') ?? null,
                'business_name' => $request->input('business_name') ?? null,
                'address' => $request->input('address'),
                'mobile' => $request->input('mobile'),
                'email' => $request->input('email'),
                'postal_code' => $request->input('postal_code') ?? null,
                'city_id' => $request->input('city_id'),
                'users_id' => $userId,
                'code_ciiu_id' => $request->input('code_ciiu_id') ?? null
            ];
            // Create body to create supplier record
            $supplierData = [
                'commercial_registry' => $request['commercial_registry'] ?? null,
                //'commercial_registry_file' => $request['commercial_registry'],
                'note' => $request->input('note') ?? null,
                'status' => $request->input('status'),
                'users_id' => $userId,
                'commercial_registry_file' => null
            ];


            // Create a record in the Third table
            $third = Third::create($thirdData);
            $supplierData['third_id'] = $third['id'];
            if($request->has('secondary_ciiu_ids')){
                $third->secondaryCiius()->attach($request['secondary_ciiu_ids'],[
                    'status' => 'A',
                    'users_id' => $userId
                ]);
            }
            //write dawn in suppliers table
            $supplier = Supplier::create($supplierData);
            $urlFile = 'supplier/'.$supplier['id'];

            if($request->has('commercial_registry_file')){
                $supplier->update([
                    'commercial_registry_file' => $request->file('commercial_registry_file')
                    ->storeAs(
                        $urlFile,
                        'crf_' . FileFormat::formatName('crf'.$request->file('commercial_registry_file')->getClientOriginalName(),
                        $request->file('commercial_registry_file')->guessExtension()))
                ]);
            }

            if($request->has('rut_file')){
                $supplier->update([
                    'rut_file' => $request->file('rut_file')
                    ->storeAs(
                        $urlFile,
                        'rutf_' . FileFormat::formatName('crf'.$request->file('rut_file')->getClientOriginalName(),
                        $request->file('rut_file')->guessExtension()))
                ]);

            }

            //append services and their fields to supplier's relationship
            foreach ($request['services'] as $svalue => $service) {
                $dynamicService = DynamicService::create([
                    'supplier_id' => $supplier->id,
                    'service_id' => $service['service_id'],
                    'status' => 'A',
                    'users_id' => $userId
                ]);
                foreach ($service['fields'] as $fvalue => $field) {
                    $content = $field['content'] ?? null;
                    if ( $content && $field['type'] == 'F' ){
                        $pathFileRequest = 'services.'.$svalue.'.fields.'.$fvalue.'.content';
                        $urlFile = $urlFile.'/services/'.$service['service_id'].'/fields';
                        $content = $request->file($pathFileRequest)
                        ->storeAs(
                            $urlFile,
                            FileFormat::formatName($request->file($pathFileRequest)->getClientOriginalName(),
                            $request->file($pathFileRequest)->guessExtension()));
                    }
                    $dynamicService->fields()->attach($field['field_id'],[
                        'path_info' => $content,
                        'users_id' => $userId,
                        'status' => 'A'
                    ]);
                }
            }
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
