<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductPlanment extends Model
{
    use HasFactory;
    protected $table = "products_planments";
    protected $fillable = ['product_id', 'planment_id', 'cost', 'discount', 'status', 'users_id', 'users_udate_id', 'amount'];
    protected $appends = ['total'];

    // Append Variables

    public function getTotalAttribute()
    {
        $total = $this->amount * $this->cost;
        // return ($total) - ($total * $this->discount/100);
        return $total - $this->discount;
    }
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function planment() : BelongsTo
    {
        return $this->belongsTo(Planment::class);
    }
    public function subproducts() : BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_planments_products', 'product_activity_id', 'product_id')->withPivot(['amount','warehouse_id']);
    }
    public function taxes() : BelongsToMany
    {
        return $this->belongsToMany(Tax::class,'products_taxes','product_planment_id', 'tax_id')->withPivot(['percent']);
    }
}
