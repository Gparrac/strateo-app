<?php

namespace App\Http\Controllers\invokes;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $cities = City::where('status','A')->where(DB::raw('UPPER(name)'),'like', '%' . strtoupper($request->input('name')) . '%')->select('id','name')->limit(10)->get();
        // $cities = City::where('status','A')->select('id','name','image1')->get();
        return response()->json(['message' => 'Read: ', 'data' => $cities], 200);
    }
}
