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
                'verification_id' => $request['tupe_document'] == 'NIT' ? CastVerificationNit::calculate($request['identification']) : NULL ,
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
            // Create body to create supplier record
            $supplierData = [
                'commercial_registry' => $request['commercial_registry'],
                //'commercial_registry_file' => $request['commercial_registry'],
                'note' => $request->input('note'),
                'status' => $request->input('status'),
                'users_id' => $userId,
                'commercial_registry_file' => ''
            ];


            // Create a record in the Third table
            $third = Third::create($thirdData);
            $supplierData['third_id'] = $third['id'];
            if($request->has('secondary_ciiu_ids')){
                $third->secondaryCiius()->attach($request['secondary_ciiu_ids'],[
                    'status' => 'A',
                    'users_id' => $userId,
                    'users_update_id' => $userId,
                ]);
            }
            //write dawn in suppliers table
            $supplier = Supplier::create($supplierData);
            $urlFile = 'supplier/'.$supplier['id'];
            $supplier->update([
                'commercial_registry_file' => $request->file('commercial_registry_file')
                ->storeAs(
                    $urlFile,
                    FileFormat::formatName('crf'.$request->file('commercial_registry_file')->getClientOriginalName(),
                    $request->file('commercial_registry_file')->guessExtension()))
            ]);
            foreach ($request['services'] as $svalue => $service) {
                $supplier->services()->attach($service['service_id'],[
                    'status' => 'A',
                    'users_id' => $userId
                ]);
                foreach ($service['fields'] as $fvalue => $field) {
                    $content = $field['content'];
                    if ($field['type'] == 'F'){
                        $pathFileRequest = 'services.'.$svalue.'.fields.'.$fvalue.'.content';
                        Log::info('controller');
                        Log::info($pathFileRequest);

                        $urlFile = $urlFile.'/services/'.$service['service_id'].'/fields/';
                        $content = $request->file($pathFileRequest)
                        ->storeAs(
                            $urlFile,
                            FileFormat::formatName($request->file($pathFileRequest)->getClientOriginalName(),
                            $request->file($pathFileRequest)->guessExtension()));
                    }
                    $supplier->fields()->attach($field['field_id'],[
                        'path_info' => $content,
                        'users_id' => $userId
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
