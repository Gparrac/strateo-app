<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductPlanment extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'planment_id', 'cost', 'discount', 'status', 'users_id', 'users_udate_id'];

    public function eventProduct() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function planment() : BelongsTo
    {
        return $this->belongsTo(Planment::class);
    }
    public function subproducts() : BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_planments_products', 'product_activity_id', 'product_id')->withPivot(['amount']);
    }
}
