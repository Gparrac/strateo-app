<?php

namespace App\Http\Controllers\CRUD\UserParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Http\Controllers\CRUD\Interfaces\RecordOperations;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteResource implements CRUD
{
    public function resource(Request $request)
    {

        $userIds = $request['users_id'];
        try {
            DB::beginTransaction();

            // Eliminar relaciones muchos a muchos con office
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                $currentStatus = $user->status == 'I' ? 'A' : 'I';
                $user = $user->update([
                    'status' => $currentStatus
                ]);
                $offices = User::find($userId)->offices;
                foreach ($offices as $key => $office) {
                    User::find($userId)->offices()->updateExistingPivot($office['id'],[
                        'status' => $currentStatus
                    ]);
                }
            }
            // Confirmar la transacción si todas las operaciones fueron exitosas
            DB::commit();

            return response()->json(['message' => 'Usuarios eliminados correctamente']);
        } catch (\Exception $e) {
            // En caso de error, revertir la transacción
            DB::rollBack();
            Log::error('unknown error UsersParameterization@createResource: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar usuarios'], 500);
        }
    }
}
