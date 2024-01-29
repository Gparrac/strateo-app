<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InventoryTrade extends Model
{
    use HasFactory;
    protected $fillable = ['status', 'note', 'transaction_date', 'supplier_id', 'users_id', 'users_update_id', 'transaction_type', 'purpose','further_discount'];

    public function inventories() : BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'inventories_inventory_trades', 'inventory_trade_id', 'inventory_id')->withPivot(['cost','amount','iva','ico','discount']);
    }
    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

}
