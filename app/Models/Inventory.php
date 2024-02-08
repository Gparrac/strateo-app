<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'stock','status', 'product_id', 'warehouse_id', 'users_id', 'users_update_id'];
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function warehouse() : BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

}
