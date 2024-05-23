<?php

namespace App\Http\Controllers\CRUD\ChargeParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Charge;
use App\Models\EmployeePlanment;
use App\Models\PaymentAccount;
use App\Models\PaymentMethod;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('charge_id')) {
            return $this->singleRecord($request->input('charge_id'));
        } else {

            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Charge::where('id', $id)
                ->first();
            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error WarehouseResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error WarehouseResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = new Charge();


            //filter query with keyword 🚨
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'name':
                        $data = $data->whereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                        break;
                    case 'id':
                        $data = $data->where('id', 'LIKE', '%' . $filter['value'] . '%');
                        break;
                    default:
                        $data = $data->whereIn('status', $filter['value']);
                        break;
                }
            }
            if ($format == 'short') {
                $data = $data->where('status', 'A')->select('charges.id', 'charges.name', 'charges.description')->take(10)->get();
            } else {
                //append shorters to query
                foreach ($sorters as $shorter) {
                    $data = $data->orderBy($shorter['key'], $shorter['order']);
                }
                $data = $data->paginate($pagination);
            }

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
