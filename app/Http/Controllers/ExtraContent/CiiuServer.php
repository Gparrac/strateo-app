<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use App\Models\Ciiu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CiiuServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if($request->has('name')){

            $codes = Ciiu::where(DB::raw('UPPER(description)'),'like', '%' . strtoupper($request->input('name')) . '%')->select('id','code','description')->limit(10)->get();
        }else{
            $codes = Ciiu::select('id','code','description')->limit(10)->get();
        }

        return response()->json(['message' => 'Read: ', 'data' => $codes], 200);
    }
}
