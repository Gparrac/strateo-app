<?php

namespace App\Http\Controllers\ExtraContent;

use App\Http\Controllers\Controller;
use App\Models\Permission;

class PermissionServer extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $forms = Permission::select('id','name')->get();
        // $forms = City::where('status','A')->select('id','name','image1')->get();
        return response()->json(['message' => 'Read: ', 'data' => $forms], 200);
    }
}
