<?php

namespace App\Http\Controllers\CRUD\ClientParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use Carbon\Carbon;

class ReadResource implements CRUD, RecordOperations
{
    public function resource(Request $request)
    {
        if ($request->has('client_id')) {
            return $this->singleRecord($request->input('client_id'));
        } else {
            return $this->allRecords(null, $request->input('pagination') ?? 5, $request->input('sorters') ?? [], $request->input('filters') ?? [], $request->input('format'));
        }
    }

    public function singleRecord($id)
    {
        try {
            $data = Client::with(['third' => function ($query) {

                    $query->with(['city:id,name','ciiu:id,code,description']);
                $query->select('id', 'type_document', 'identification', 'code_ciiu_id', 'verification_id', 'names', 'surnames', 'business_name', 'address', 'mobile', 'email', 'email2', 'postal_code', 'city_id');
            }])
                ->where('clients.id', $id)
                ->first();

            return response()->json(['message' => 'read: ' . $id, 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:singleRecord: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }

    public function allRecords($ids = null, $pagination = 5, $sorters = [], $filters = [], $format = null)
    {
        try {
            $data = Client::with('third:id,names,surnames,identification,email');;

            //filter query with keyword 🚨
            if ($filters) {
                foreach ($filters as $filter) {
                    switch ($filter['key']) {
                        case 'client':
                            $data = $data->whereRaw("UPPER(CONCAT(clients.legal_representative_name,'',IFNULL(clients.legal_representative_id,''))) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                            break;
                        case 'third':
                            $data->whereHas('third', function($query) use ($filter){
                                $query->whereRaw("UPPER(CONCAT(thirds.names, ' ',thirds.surnames, ' ' ,IFNULL(thirds.identification,''))) LIKE ?", ['%' . strtoupper($filter['value']) . '%']);
                            });
                            break;
                        case 'dateInvoice':
                            $data = $data->whereHas('invoice', function($query) use ($filter) {
                                $query->whereBetween('invoices.created_at', [$filter['value']['start_date'], $filter['value']['start_date']]);
                            });
                            break;
                        case 'status':
                            $data = $data->whereIn('status', $filter['value']);
                            break;
                        default:
                            $data = $data->where('id','LIKE', '%' . $filter['value'] . '%');
                            break;
                    }
                }
            }
            if($format == 'short'){
                $data = $data->where('status','A')->take(10)->get();
                $data = $data->map(function($client){
                    return [
                        'id' => $client['id'],
                        'name' => $client['third']['fullname'],
                        'document' => $client['third']['identification']
                    ];
                });
            }else{
                $data = $data->with('third:id,names,business_name,surnames,identification,email,type_document')
                            ->select('id', 'status', 'legal_representative_name', 'legal_representative_id', 'third_id', 'updated_at');
                if($format == 'analytic'){
                    $data->withCount(['invoices' => function($query) use ($filter){
                        $query->whereBetween('invoices.created_at', [$filter['value']['start_date'], $filter['value']['start_date']]);
                    }]);
                }

            //append shorters to query
            foreach ($sorters as $shorter) {

                $data = $data->orderBy($shorter['key'], $shorter['order']);
            }
            $data = $data->paginate($pagination);
            }

            return response()->json(['message' => 'read', 'data' => $data], 200);
        } catch (QueryException $ex) {
            Log::error('Query error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error ClientResource@readResource:allRecords: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'read u'], 500);
        }
    }
}
