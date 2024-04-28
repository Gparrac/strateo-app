<?php

namespace App\Http\Controllers\CRUD\PaymentParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\EmployeePlanment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class ReadResource implements CRUD
{
    public function resource(Request $request)
    {
        return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
    }



    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = EmployeePlanment::with(['charges' => function ($query) {
                $query->where('status', 'A')->select('charges.id', 'charges.name');
            }, 'paymentAccount' => function ($query) {
                $query->with('paymentMethod:id,name');
                $query->where('status', 'A')->select('id', 'name');
            }, 'employee' => function ($query) {
                $query->with('third:id,names,surnames,identification,type_document');
                $query->where('status', 'A');
                $query->select('employees.id', 'hire_date', 'end_date', 'type_contract');
            }]);
            //append shorters to query
            foreach ($sorters as $shorter) {
                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
