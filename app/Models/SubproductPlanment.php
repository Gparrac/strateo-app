<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubproductPlanment extends Model
{
    use HasFactory;
    protected $table = 'subproducts_planments';
    protected $fillable = ['planment_id','product_id','tracing','warehouse_id','users_id'];

    public function product() : BelongsTo
    {
        return $this->BelongsTo(Product::class);
    }
    public function productPlanments() : BelongsToMany
    {
        return $this->belongsToMany(ProductPlanment::class,'product_planments_subproduct_planments', 'subproduct_planment_id', 'product_planment_id')->withPivot('amount');
    }
    public function warehouse() : BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function planment() : BelongsTo
    {
        return $this->belongsTo(Planment::class);
    }
}
