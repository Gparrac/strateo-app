<?php

namespace App\Http\Controllers\CRUD\BrandParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            DB::beginTransaction();
            $userId = Auth::id();

            Brand::create([
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'status' => $request->input('status'),
                'users_id' => $userId,
            ]);
            DB::commit();
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            DB::rollback();
            Log::error('Query error BrandResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('unknown error BrandResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
