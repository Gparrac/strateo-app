<?php

namespace App\Http\Controllers\CRUD\EmployeeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Utils\CastVerificationNit;
use App\Models\DynamicService;
use App\Models\Planment;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Third;

use App\Http\Utils\FileFormat;
use App\Models\Employee;
use App\Models\EmployeePaymentMethod;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {

        DB::beginTransaction();
        try {
            $userId = auth()->id();
            // request data to update
            if ($request->has('type_connection')) {
                $this->createEmployeePlanment($request, $userId);
            } else {
                $this->createResource($request, $userId);
            }
            // Create body to create third record

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
    protected function createResource($request, $userId)
    {
        $thirdData = [
            'type_document' => $request->input('type_document'),
            'identification' => $request->input('identification'),
            'verification_id' => $request['tupe_document'] == 'NIT' ? CastVerificationNit::calculate($request['identification']) : NULL,
            'names' => $request->input('names') ?? null,
            'surnames' => $request->input('surnames') ?? null,
            'business_name' => $request->input('business_name') ?? null,
            'address' => $request->input('address'),
            'mobile' => $request->input('mobile'),
            'email' => $request->input('email'),
            'postal_code' => $request->input('postal_code') ?? null,
            'city_id' => $request->input('city_id'),
            'users_id' => $userId,
        ];
        // Create body to create employee's record
        $employeeData = [
            'type_contract' => $request['type_contract'],
            'hire_date' => $request['hire_date'],
            'end_date_contract' => $request['end_date_contract'],
            'status' => $request['status'],
            'users_id' => $userId
        ];
        // Create a record in the Third table
        $third = Third::create($thirdData);
        $employeeData['third_id'] = $third['id'];
        //write down in employee's table
        $employee = Employee::create($employeeData);
        $urlFile = 'employees/' . $employee['id'];

        $employee->update([
            'rut_file' => $request->has('rut_file') ? $request->file('rut_file')
                ->storeAs(
                    $urlFile,
                    FileFormat::formatName(
                        'crf' . $request->file('rut_file')->getClientOriginalName(),
                        $request->file('rut_file')->guessExtension()
                    )
                ) : null,
            'resume_file' => $request->has('resume_file') ? $request->file('resume_file')
                ->storeAs(
                    $urlFile,
                    FileFormat::formatName(
                        'crf' . $request->file('resume_file')->getClientOriginalName(),
                        $request->file('resume_file')->guessExtension()
                    )
                ) : null,
        ]);
        foreach ($request['payment_methods'] as $svalue => $service) {
            $dynamicService = EmployeePaymentMethod::create([
                'employee_id' => $employee->id,
                'payment_method_id' => $service['payment_method_id'],
                'status' => 'A',
                'users_id' => $userId,
                'reference' => $service['reference'],
                'users_update_id' => $userId
            ]);
        }
        //relate services and its fields with employee record
        //append services and their fields to supplier's relationship
        foreach ($request['services'] as $svalue => $service) {
            $dynamicService = DynamicService::create([
                'employee_id' => $employee->id,
                'service_id' => $service['service_id'],
                'status' => 'A',
                'users_id' => $userId
            ]);
            foreach ($service['fields'] as $fvalue => $field) {
                $content = $field['content'] ?? null;
                if ($content && $field['type'] == 'F') {
                    $pathFileRequest = 'services.' . $svalue . '.fields.' . $fvalue . '.content';
                    $urlFile = $urlFile . '/services/' . $service['service_id'] . '/fields';
                    $content = $request->file($pathFileRequest)
                        ->storeAs(
                            $urlFile,
                            FileFormat::formatName(
                                $request->file($pathFileRequest)->getClientOriginalName(),
                                $request->file($pathFileRequest)->guessExtension()
                            )
                        );
                }
                $dynamicService->fields()->attach($field['field_id'], [
                    'path_info' => $content,
                    'users_id' => $userId,
                    'status' => 'A'
                ]);
            }
        }
    }
    protected function createEmployeePlanment(Request $request, $userId)
    {
        foreach ($request['employees'] as $employee) {
            Planment::find('planment_id')->employees()->attach($employee['employee_id'], [
                'salary' => $employee['salary'],
                'users_id' => $userId
            ]);
        }
    }
}
