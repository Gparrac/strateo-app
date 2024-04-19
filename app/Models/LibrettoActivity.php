<?php

namespace App\Models;

use App\Casts\FileCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LibrettoActivity extends Model
{
    use HasFactory;
    protected $table = "libretto_activities";
    protected $fillable = ['name', 'description', 'path_file', 'users_id', 'users_update_id', 'status'];

    protected $casts = [
        'path_file' => FileCast::class
    ];
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'libretto_activities_products', 'libretto_activity_id','product_id')->withPivot(['status']);
    }
    public function planments(): BelongsToMany
    {
        return $this->belongsToMany(Planment::class, 'libretto_activities_planments','libretto_activity_id', 'planment_id')->withPivot(['status', 'description', 'path_file'])->using(LibrettoActivityPlanment::class);
    }
}
