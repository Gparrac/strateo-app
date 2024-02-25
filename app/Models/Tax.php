<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tax extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'acronym', 'default_percent','status','users_update_id','users_id'];

    public function products() :BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_taxes','tax_id', 'product_id')->withPivot('percent');
    }
}
