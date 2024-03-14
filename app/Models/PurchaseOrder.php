<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_order';

    protected $fillable = [
        'supplier_id',
        'date',
        'note',
        'users_id',
        'users_update_id',
        'status'
    ];

    // RELATIONSHIP
    public function products() : BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_orders_products', 'purchase_order_id', 'product_id')
        ->withPivot(['amount','users_id', 'users_update_id']);
    }

    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
