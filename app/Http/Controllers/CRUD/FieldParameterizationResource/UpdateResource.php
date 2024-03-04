<?php

namespace App\Http\Controllers\CRUD\FieldParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Third;
use App\Models\Client;
use App\Models\User;
use App\Http\Utils\FileFormat;
use App\Models\Field;
use App\Models\Service;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            // Client update
            $service = Field::findOrFail($request->input('field_id'));
            //Save the new files

            $service->fill($request->only([
                'name',
                'type',
                'length',
                'status',
            ])+ ['users_update_id' => $user->id])->save();

            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error FieldResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error FieldResource@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
