<?php

namespace App\Http\Controllers\CRUD\LibrettoActivityParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;
use App\Models\Third;
use Illuminate\Support\Facades\Auth;
use App\Http\Utils\CastVerificationNit;
use App\Http\Utils\FileFormat;
use App\Models\LibrettoActivity;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        DB::beginTransaction();
        Log::info('creando libreto');

        try {
            $userId = Auth::id();
            $data = [
                'name' => $request->input('name'),
                'description' => $request->input('description') ?? null,
                'users_id' => $userId,
                'status' => $request->input('status')
            ];
            // Create a record in the Third table
            $la = LibrettoActivity::create($data);
            $la->products()->attach($request->input('product_ids'), [
                'status' => 'A',
                'users_id' => auth()->id()
            ]);
            if($request->hasFile('file')){
                $la->update([
                    'path_file' => $request->file('file')
                    ->storeAs(
                        'librettoActivities',
                        FileFormat::formatName($request->file('file')->getClientOriginalName(),
                        $request->file('file')->guessExtension()))
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error WarehouseResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
