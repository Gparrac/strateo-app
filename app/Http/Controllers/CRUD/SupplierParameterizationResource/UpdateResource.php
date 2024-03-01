<?php

namespace App\Http\Controllers\CRUD\SupplierParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Utils\FileFormat;
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
                            'crf' . $request->file('commercial_registry_file')->getClientOriginalName(),
                            $request->file('commercial_registry_file')->guessExtension()
                        )
                    );
            }
            if ($request->hasFile('rut_file')) {
                $supplier->rut_file = $request->file('rut_file')
                    ->storeAs(
                        $urlFile,
                        FileFormat::formatName(
                            $request->file('rut_file')->getClientOriginalName(),
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
                            'users_update_id' => $userId
                        ]);
                    }else{
                        $codes->update([
                            'status' => 'A'
                        ]);
                    }
                }
            }

            // update services and related fields
            DB::table('suppliers_services')->where('suppliers_id', $supplier['id'])->update(['status' => 'I', 'users_update_id' => $userId]);
            foreach ($request['services'] as $svalue => $service) {
                $query = DB::table('suppliers_services')->where('suppliers_id', $supplier['id'])->where('services_id', $service['service_id']);
                if ($query->count() == 0) {
                    $supplier->fields()->attach($service['service_id'], [
                        'status' => 'A',
                        'users_id' => $userId
                    ]);
                } else {
                    $query->update([
                        'status' => 'A',
                        'users_update_id' => $userId
                    ]);
                }
                foreach ($service['fields'] as $fvalue => $field) {

                    if ($field['type'] == 'F') {
                        if(!array_key_exists('content',$field)){
                            $content = Field::find($field['field_id'])->suppliers()->where('suppliers_id',$supplier['id'])->first()->pivot['path_info'];
                        }else{
                        //if update service and its a file then it's gonna create other file and not replace
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
                    $queryFields = DB::table('suppliers_fields')->where('suppliers_id', $supplier['id'])->where('fields_id', $field['field_id']);
                    if ($query->count() == 0) {
                        $supplier->fields()->attach($field['field_id'], [
                            'path_info' => $content,
                            'users_id' => $userId
                        ]);
                    } else {
                        $queryFields->update([
                            'path_info' => $content,
                            'users_update_id' => $userId
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
