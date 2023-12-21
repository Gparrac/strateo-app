<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckEnterpriseCreation extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        try {
            $third = User::find(Auth::id())->third->id;
            $companyExist = Company::where('third_id', $third)->count() == 0 ? false : true;
            return response()->json(['message' => 'Successful', 'data' => $companyExist]);
        } catch (QueryException $ex) {
            // In case of error, roll back the transaction
            Log::error('Query error CompanyParameterization@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
        } catch (\Exception $ex) {
            // In case of error, roll back the transaction
            Log::error('unknown error CompanyParameterization@updateResource: - Line:' . $ex->getLine() . ' - message: ' . $ex->getMessage());
            return response()->json(['message' => 'update u'], 500);
        }
    }
}
