<?php

namespace App\Http\Controllers\CRUD\TaxValueParameterizationResource;

use App\Http\Controllers\CRUD\Interfaces\CRUD;
use App\Models\TaxValue;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tax;

class CreateResource implements CRUD
{
    public function resource(Request $request)
    {
        try {
            $userId = Auth::id();
            $taxValue =  TaxValue::create([
                'percent' => $request->input('percent'),
                'users_id' => $userId
            ]);

            $taxValue = ['id' => $taxValue['id'], 'percent' => $taxValue['percent']];
            return response()->json(['message' => 'Successful', 'data'=>$taxValue ]);
        } catch (QueryException $ex) {
            Log::error('Query error TaxResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create q'], 500);
        } catch (\Exception $ex) {
            Log::error('unknown error TaxResource@create: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'create u'], 500);
        }
    }
}
