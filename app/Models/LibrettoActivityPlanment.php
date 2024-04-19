<?php

namespace App\Models;

use App\Casts\FileCast;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LibrettoActivityPlanment extends Pivot
{
    protected $table = 'libretto_activities_products';
    protected $fillable = ['path_file', 'description'];
    protected $casts = [
        'path_file' => FileCast::class
    ];
}
