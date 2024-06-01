<?php

namespace App\Http\Controllers\Metrics\Services;
use App\Http\Controllers\Metrics\Interfaces\FactoryService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellerAnalytics implements FactoryService
{
    function buildService($request, $authId){
        try {
            $data = [];
            DB::beginTransaction();
            switch ($request['option']) {
                case 'TA':
                    $data = $this->getTotalActive($request['start_date'], $request['end_date']);
                    break;
                case 'LA':
                    $data = $this->getActive($request['keyword'] ?? null, $pagination = 5, $request['start_date'], $request['end_date']);
                    break;
                case 'LS':
                        $data = $this->getActiveTop( $request['start_date'], $request['end_date']);
                        break;
                default:
                    # code...
                    break;
            }
            // Confirmar la transacción si todas las operaciones fueron exitosas
            DB::commit();

            return response()->json(['message' => 'Total activos en este periodo', 'data' =>$data]);
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();
            Log::error('unknown error ClientAnalytics: ' . $e->getMessage());
            return response()->json(['error' => 'Error obtener metrica'], 500);
        }
    }

    private function getActive($keyword, $pagination, $startDate, $endDate){

        $data =  User::select('id','name','third_id')->withCount(['invoices' => function($query) use ($startDate, $endDate) {
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
    private function getTotalActive($startDate, $endDate){
        $data =  User::select('id')
        ->whereHas('invoices', function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoices.created_at', [$startDate, $endDate]);
        })->count();
        return $data;
    }
    private function getActiveTop($startDate, $endDate){
        $customers = [];
        $invoices = [];
        User::select('id','name','third_id')
        ->withCount(['invoices' => function($query) use ($startDate, $endDate) {
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
