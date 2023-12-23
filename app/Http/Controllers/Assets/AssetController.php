<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AssetController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $filefolder, $filename)
    {
        try {
            $path = public_path("/uploads/{$filefolder}/{$filename}");
            if (File::exists($path)) {
                return response()->file($path);
            }
            abort(404, 'not_found');
        } catch (\Throwable $th) {
            abort(404, 'not_found');
        }
    }
}
