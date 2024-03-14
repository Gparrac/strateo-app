<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LibrettoActivity extends Model
{
    use HasFactory;
    protected $table = "libretto_activities";
    protected $fillable = ['name', 'description', 'users_id', 'users_update_id', 'status'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'libretto_activities_products', 'libretto_activity_id','product_id')->withPivot(['status']);
    }
    public function planments(): BelongsToMany
    {
        return $this->belongsToMany(Planment::class, 'libretto_activities_planments','libretto_activity_id', 'planment_id')->withPivot(['status']);
    }
}
