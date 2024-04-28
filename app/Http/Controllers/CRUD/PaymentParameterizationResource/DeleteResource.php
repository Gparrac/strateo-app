<?php

namespace App\Http\Controllers\CRUD\PaymentParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\EmployeePlanment;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if($request->has('employee_planment_id')){
            return $this->singleRecord($request->input('employee_planment_id'));
        }else{
            return $this->allRecords($request->input('employee_planment_ids'));
        }
    }

    public function singleRecord($id){
        try {
            $userId = auth()->id();
            $employeePlanment = EmployeePlanment::where('id', $id)->firstOrFail();

            // Create a record in the Office table
            $employeePlanment->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            return response()->json(['message' => 'Delete: '. $id], 200);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }

    public function allRecords($ids = null,$pagination=5, $sorters = [], $keyword =null, $typeKeyword = null){
        try {
            $userId = auth()->id();

            EmployeePlanment::whereIn('id', $ids)->update([
                'status' => 'I',
                'users_update_id' => $userId,
            ]);
            DB::table('charges_employee_planments')->where('employee_planment_id',$ids)->update([
                'users_update_id' => $userId,
                'status' => 'I'
            ]);
            return response()->json(['message' => 'Delete: '.join(',',$ids)], 200);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'delete u'], 500);
        }
    }
}
