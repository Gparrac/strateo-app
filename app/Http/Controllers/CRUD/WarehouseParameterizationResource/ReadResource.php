<?php

namespace App\Http\Controllers\CRUD\WarehouseParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('warehouse_id')) {
            return $this->singleRecord($request->input('warehouse_id'));
        } else {

            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Warehouse::where('id', $id)
                ->with(['city:id,name', 'third' => function ($query) {
                    $query->select('id', 'type_document', 'identification', 'code_ciiu_id', 'verification_id', 'names', 'surnames', 'business_name', 'address', 'mobile', 'email', 'email2', 'postal_code', 'city_id');
                    $query->with([
                        'ciiu:id,code,description', 'city:id,name',
                        'secondaryCiius' => function ($query) {
                            $query->where('status', 'A')->select('code_ciiu_thirds.id', 'code', 'description');
                        }
                    ]);
                }])
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
            $data = Warehouse::select('id', 'note', 'status', 'city_id', 'address', 'updated_at')
                ->with('city:id,name');

            //filter query with keyword 🚨
            foreach ($filters as $filter) {
                switch ($filter['key']) {
                    case 'city':
                        $data = $data->whereHas('city', function ($query) use ($filter) {
                            $query->whereRaw("UPPER(cities.name) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                        });
                        break;
                    case 'address':
                        $data = $data->whereRaw("UPPER(warehouses.address) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                        break;
                    case 'status':
                        $data = $data->whereIn('status', $filter['value']);
                        break;
                    default:
                        $data = $data->where('id', 'LIKE', '%' . $filter['value'] . '%');
                        break;
                }
            }
            if ($format == 'short') {
                $data = $data->where('status', 'A')->select('warehouses.id', 'warehouses.address', 'warehouses.city_id')->take(10)->get();
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
