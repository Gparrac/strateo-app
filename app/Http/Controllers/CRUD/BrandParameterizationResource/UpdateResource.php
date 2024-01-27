<?php

namespace App\Http\Controllers\CRUD\BrandParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Brand;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = Auth::id();
            $brand = Brand::where('id', $request->input('brand_id'))->firstOrFail();

            $brand->fill($request->only([
                'name',
                'code',
                'status',
            ]) + ['users_update_id' => $userId])->save();

            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error BrandResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error BrandResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
