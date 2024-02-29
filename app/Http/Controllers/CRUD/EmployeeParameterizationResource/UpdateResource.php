<?php

namespace App\Http\Controllers\CRUD\EmployeeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Utils\FileFormat;
use App\Models\Employee;
use App\Models\Field;
use App\Models\Invoice;
use App\Models\Planment;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Third;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->id();
            // request data to update
            if($request->has('type_connection')){
                $this->updateEmployeePlanment($request, $userId);
            }else{
                $this->updateResource($request,$userId);
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
    protected function updateResource(Request $request, $userId){
        $employee = Employee::findOrFail($request->input('employee_id'));
        $third = Third::findOrFail($employee['third_id']);
        $urlFile = 'employees/' . $employee['id'];
        // save suppllier's files
        if ($request->hasFile('rut_file')) {
            $employee->rut_file = $request->file('rut_file')
                ->storeAs(
                    $urlFile,
                    FileFormat::formatName(
                        'crf' . $request->file('rut_file')->getClientOriginalName(),
                        $request->file('rut_file')->guessExtension()
                    )
                );
        }
        if ($request->hasFile('resume_file')) {
            $employee->resume_file = $request->file('resume_file')
                ->storeAs(
                    $urlFile,
                    FileFormat::formatName(
                        $request->file('resume_file')->getClientOriginalName(),
                        $request->file('resume_file')->guessExtension()
                    )
                );
        }
        // update supplier and third's records
        $employee->fill($request->only([
            'type_contract',
            'hire_date',
            'end_date_contract',
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
        ]) + ['users_update_id' => $userId])->save();

        // update services and related fields
        DB::table('services_employees')->where('employee_id', $employee['id'])->update(['status' => 'I', 'users_update_id' => $userId]);
        Log::info("paso 2");
        foreach ($request['services'] as $svalue => $service) {
            $query = DB::table('services_employees')->where('employee_id', $employee['id'])->where('service_id', $service['service_id']);
            Log::info("paso 3");
            if ($query->count() == 0) {
                $employee->services()->attach($service['service_id'], [
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
                        $content = Field::find($field['field_id'])->employees()->where('employee_id',$employee['id'])->first()->pivot['path_info'];
                        Log::info("paso 4");
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
                $queryFields = DB::table('fields_employees')->where('employee_id', $employee['id'])->where('field_id', $field['field_id']);
                Log::info("paso 5");
                if ($query->count() == 0) {
                    Log::info("paso 6");
                    $employee->fields()->attach($field['field_id'], [
                        'path_info' => $content,
                        'users_id' => $userId
                    ]);
                    Log::info("paso 7");
                } else {
                    $queryFields->update([
                        'path_info' => $content,
                        'users_update_id' => $userId
                    ]);
                    Log::info("paso 8");
                }
                Log::info("paso 8.5");
            }
            Log::info("paso 9");
        }

    }
    protected function updateEmployeePlanment(REquest $request, $userId){
        $planment = Invoice::find($request['invoice_id'])->planment;
        Log::info('entrando');
        Log::info($planment);
        $savedEmployees = Planment::find($planment->id)->employees()->pluck('employees.id')->toArray();
        Log::info($savedEmployees);
        Planment::find($planment->id)->employees()->detach($savedEmployees);

        foreach ($request['employees'] as $employee) {
            Planment::find($planment->id)->employees()->attach($employee['employee_id'], [
                'salary' => $employee['salary'],
                'users_id' => $userId,
                'status' => 'A'
            ]);

        }
    }
}
