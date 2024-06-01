<?php

namespace App\Http\Controllers\Metrics\Services;
use App\Http\Controllers\Metrics\Interfaces\FactoryService;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientAnalytics implements FactoryService
{
    function buildService($request, $authId){
        try {
            DB::beginTransaction();
            switch ($request['option']) {
                case 'TA':
                    $data = $this->getTotalActiveClients($request['start_date'], $request['end_date']);
                    break;
                case 'LA':
                    $data = $this->getActiveClients($request['keyword'] ?? null, $pagination = 5, $request['start_date'], $request['end_date']);
                    break;
                case 'LS':
                        $data = $this->getActiveClientsTop( $request['start_date'], $request['end_date']);
                        break;
                default:
                    # code...
                    break;
            }

            // Confirmar la transacción si todas las operaciones fueron exitosas
            DB::commit();

            return response()->json(['message' => 'Total activos en este periodo', 'data' => $data]);
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();
            Log::error('unknown error ClientAnalytics: ' . $e->getMessage());
            return response()->json(['error' => 'Error obtener metrica'], 500);
        }
    }
    private function getActiveClients($keyword, $pagination, $startDate, $endDate){

        $data =  Client::select('id', 'legal_representative_name', 'legal_representative_id', 'third_id')->withCount(['invoices' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        }])
        ->whereHas('invoices', function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        })->with('third:id,names,surnames,identification,type_document');
        if($keyword) $data = $data->whereHas('third',function($query) use ($keyword){
            $query->whereRaw("UPPER(CONCAT(thirds.names, ' ',thirds.surnames, ' ' ,IFNULL(thirds.identification,''))) LIKE ?", ['%' . strtoupper($keyword) . '%']);
        });

        $data = $data->orderBy('invoices_count', 'desc')->paginate($pagination);
        return $data;
    }
    private function getTotalActiveClients($startDate, $endDate){
        $data =  Client::select('id')->withCount(['invoices' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        }])
        ->whereHas('invoices', function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        })->count();
        return $data;
    }
    private function getActiveClientsTop($startDate, $endDate){
        $customers = [];
        $invoices = [];
        Client::select('id', 'legal_representative_name', 'legal_representative_id', 'third_id')->withCount(['invoices' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        }])
        ->whereHas('invoices', function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        })->with('third:id,names,surnames,identification,type_document')
        ->orderBy('invoices_count', 'desc')->take(5)->each(function($query) use (&$customers, &$invoices){
            array_push($customers, $query['third']['fullname']);
            array_push($invoices, $query['invoices_count']);
        });
        return [
            'customers' => $customers,
            'invoices' => $invoices
        ];
    }
}
