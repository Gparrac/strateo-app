<?php

namespace App\Http\Controllers\Metrics\Services;
use App\Http\Controllers\Metrics\Interfaces\FactoryService;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceAnalytics implements FactoryService
{
    function buildService($request, $authId){
        try {
            $data = [];
            DB::beginTransaction();
            switch ($request['option']) {
                case 'TA':
                    $data = $this->getTotalActive($request['start_date'], $request['end_date']);
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

    private function getTotalActive($startDate, $endDate){

        $data =  Invoice::
        whereBetween('invoices.created_at', [$startDate, $endDate])
        ->count();

        return $data;


    }

}
