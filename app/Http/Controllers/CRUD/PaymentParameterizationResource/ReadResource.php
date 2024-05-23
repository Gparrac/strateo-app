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
            $data = EmployeePlanment::with(['charges:id,name', 'employee' => function ($query) {
                $query->with('third:id,names,surnames,identification,type_document');
                $query->select('employees.id', 'hire_date', 'end_date_contract', 'type_contract', 'employees.third_id');
            }, 'paymentMethod:id,name', 'planment' => function ($query) {
                $query->with(['invoice' => function ($query) {
                    $query->with(['client' => function ($query) {
                        $query->with('third:id,names,surnames');
                        $query->select('clients.id', 'clients.third_id');
                    }]);
                    $query->select('invoices.id', 'invoices.client_id');
                }]);
                $query->select('planments.id', 'planments.invoice_id');
            }]);
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'employee':
                        $data->whereHas('employee', function ($query) use ($filter) {
                            $query->whereHas('third', function ($query) use ($filter) {

                                $query->whereRaw("UPPER(CONCAT(IFNULL(thirds.surnames,''),IFNULL(thirds.names,''),IFNULL(thirds.business_name,''),IFNULL(thirds.identification,''))) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                            });
                        });
                        break;
                    case 'status':
                        $data = $data->whereIn('status', $filter['value']);
                        break;
                    case 'settled':
                        $data = $data->whereIn('settled', $filter['value']);
                        break;
                    case 'client':
                        $data = $data->whereHas('planment', function ($query) use ($filter) {
                            $query->whereHas('invoice', function ($query) use ($filter) {
                                $query->whereHas('client', function ($query) use ($filter) {
                                    $query->whereHas('third', function ($query) use ($filter) {
                                        $query->whereRaw("UPPER(CONCAT(IFNULL(thirds.surnames,''),IFNULL(thirds.names,''),IFNULL(thirds.business_name,''),IFNULL(thirds.identification,''))) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                                    });
                                });
                            });
                        });
                        break;
                    case 'planment':
                        $data = $data->where('employees_planments.planment_id', $filter['value']);
                        break;

                    default:
                        $data = $data->where('id', 'LIKE', '%' . $filter['value'] . '%');
                        break;
                }
            }
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
