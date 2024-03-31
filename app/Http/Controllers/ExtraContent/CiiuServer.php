<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use App\Models\Ciiu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CiiuServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if($request->has('code')){
            Log::info('etrnyyy');
            // $codes = Ciiu::where('code','like', '%'. $request->input('code') . '%')->select('id','code','description')->limit(10)->get();
            $codes = Ciiu::where(DB::raw('UPPER(CONCAT(description,code))'),'like', '%' . strtoupper($request->input('code')) . '%')->select('id','code','description')->limit(10)->get();
        }else{
            $codes = Ciiu::select('id','code','description')->limit(10)->get();
        }

        return response()->json(['message' => 'Read: ', 'data' => $codes], 200);
    }
}
