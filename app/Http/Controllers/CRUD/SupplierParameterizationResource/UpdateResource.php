<?php

namespace App\Http\Controllers\CRUD\SupplierParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Utils\FileFormat;
use App\Models\DynamicService;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Third;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            // request data to update
            $supplier = Supplier::findOrFail($request->input('supplier_id'));
            $third = Third::findOrFail($supplier['third_id']);
            $urlFile = 'supplier/' . $supplier['id'];
            // save suppllier's files
            if ($request->hasFile('commercial_registry_file')) {
                $supplier->commercial_registry_file = $request->file('commercial_registry_file')
                    ->storeAs(
                        $urlFile,
                        FileFormat::formatName(
                            'crf_' . $request->file('commercial_registry_file')->getClientOriginalName(),
                            $request->file('commercial_registry_file')->guessExtension()
                        )
                    );
            }
            if ($request->hasFile('rut_file')) {
                $supplier->rut_file = $request->file('rut_file')
                    ->storeAs(
                        $urlFile,
                        FileFormat::formatName(
                            'rutf_' . $request->file('rut_file')->getClientOriginalName(),
                            $request->file('rut_file')->guessExtension()
                        )
                    );
            }
            // update supplier and third's records
            $supplier->fill($request->only([
                'commercial_registry',
                'legal_representative_name',
                'legal_representative_id',
                'note',
                'status',
            ]) + ['users_update_id' => $userId])->save();
            //third update
            $third->fill($request->only([
                'type_document',
                'identification',
                'names',
                'surnames',
                'address',
                'mobile',
                'email',
                'email2',
                'postal_code',
                'city_id',
                'code_ciiu_id',
            ]) + ['users_update_id' => $userId])->save();
            //secondary ciiu thirds
            DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->update(['status' => 'I']);
            if($request->has('secondary_ciiu_ids')){
                foreach ($request['secondary_ciiu_ids'] as $key => $value) {
                    $codes = DB::table('code_ciiu_thirds')->where('thirds_id',$third['id'])->where('code_ciiu_id', $value);
                    if($codes->count() == 0){
                        $third->secondaryCiius()->attach($value,[
                            'status' => 'A',
                            'users_id' => $userId,

                        ]);
                    }else{
                        $codes->update([
                            'status' => 'A',
                            'users_update_id' => $userId
                        ]);
                    }
                }
            }
            // update services and related fields
            $savedServices = DynamicService::where('supplier_id', $supplier['id'])->pluck('service_id')->toArray();
            $newServices = array_column($request['services'], 'service_id');
            $inactiveServices = array_diff($savedServices, $newServices);
            DynamicService::where('supplier_id', $supplier['id'])->whereIn('service_id',$inactiveServices)->update(['status'=> 'I', 'users_update_id' => $userId]);

            foreach ($request['services'] as $svalue => $service) {
                $query = DynamicService::where('supplier_id', $supplier['id'])->where('service_id', $service['service_id'])->first();
                if ($query) {
                    $query->update([
                        'status' => 'A',
                        'users_update_id' => $userId
                    ]);
                } else {
                    $query = DynamicService::create([
                        'status' => 'A',
                        'supplier_id' => $supplier['id'],
                        'service_id' => $service['service_id'],
                        'users_id' => $userId
                    ]);
                }
                DB::table('fields_dynamic_services')->where('dynamic_service_id', $query['id'])->update(['status' => 'I', 'users_update_id' => $userId]);
                foreach ($service['fields'] as $fvalue => $field) {
                    if ($field['type'] == 'F') {
                        if(!array_key_exists('content', $field)){
                            $content = $query->fields()->where('fields.id',$field['field_id'])->first();
                            $content = $content ? $content->pivot->path_info : null;
                        }else{
                        $pathFileRequest = 'services.' . $svalue . '.fields.' . $fvalue . '.content';
                        $urlFile = $urlFile . '/services/' . $service['service_id'] . '/fields/';
                        $content = $request->file($pathFileRequest)
                            ->storeAs(
                                $urlFile,
                                FileFormat::formatName(
                                    $request->file($pathFileRequest)->getClientOriginalName(),
                                    $request->file($pathFileRequest)->guessExtension()
                                )
                            );
                        }
                    }else{
                        $content = $field['content'];
                    }


                    $queryFields = $query->fields()->where('fields.id',$field['field_id'])->first();
                    if ($queryFields) {
                        DB::table('fields_dynamic_services')->where('dynamic_service_id', $query['id'])
                        ->where('field_id',$queryFields['id'])->update([
                            'path_info' => $content,
                            'users_update_id' => $userId
                        ]);
                    } else {
                        $query->fields()->attach($field['field_id'], [
                            'path_info' => $content,
                            'users_id' => $userId,
                            'status'=> 'A',
                        ]);
                    }

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
