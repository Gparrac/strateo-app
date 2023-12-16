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
        Log::info('passando!');
        $userIds = $request['users_id'];
        try {
            DB::beginTransaction();

            User::whereIn('id', $userIds)->update([
                'status' => 'I'
            ]);

            // Eliminar relaciones muchos a muchos con office
            foreach ($userIds as $userId) {
                $offices = User::find($userId)->offices;
                // return response()->json(['message' => User::find($userId)->offices]);
                foreach ($offices as $key => $office) {
                    User::find($userId)->offices()->updateExistingPivot($office['id'],[
                        'status' => 'I'
                    ]);
                }
            }
            Log::info('passando!');
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
