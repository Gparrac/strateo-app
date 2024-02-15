<?php

namespace App\Http\Utils;

use Illuminate\Support\Str;

class FileFormat
{
    public static function formatName($fileName, $extension)
    {
        return substr($fileName, 0, 3) .
            '_' .
            now()->format('Ymd_His') .
            '_' .
            Str::random(10) .
            '.' .
            $extension;
    }
    public static function downloadPath($fileName)
    {
        return config('app.files_url').'/uploads/'.$fileName;
    }
}
