<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FurtherProductPlanment extends Model
{
    use HasFactory;
    protected $table = "further_products_planments";
    protected $fillable = ['product_id', 'planment_id', 'amount', 'status','discount', 'cost','warehouse_id','tracing' ,'users_id', 'users_udate_id'];

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function planment() : BelongsTo
    {
        return $this->belongsTo(Planment::class);
    }
    public function taxes() : BelongsToMany
    {
        return $this->belongsToMany(Tax::class,'products_taxes','further_product_planment_id','tax_id')->withPivot('percent');
    }
}
