<?php

namespace App\Http\Controllers\CRUD\TaxParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Tax;

class UpdateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = Auth::id();
            $tax = Tax::where('id', $request->input('tax_id'))->firstOrFail();

            $tax->fill($request->only([
                'name',
                'acronym',
                'status',
                'type',
                'context'
            ]) + ['users_update_id' => $userId])->save();
            $tax->taxValues()->detach();
            foreach ($request->input('values') as $value) {
                $tax->taxValues()->attach($value['tax_value_id']);
            }
            return response()->json(['message' => 'Successful']);
        } catch (QueryException $ex) {
            Log::error('Query error TaxResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error TaxResource@update: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
