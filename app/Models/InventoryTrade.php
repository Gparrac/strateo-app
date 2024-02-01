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
        return $this->belongsToMany(Inventory::class, 'inventories_inventory_trades', 'inventory_trade_id', 'inventory_id')->withPivot(['cost','amount']);
    }
    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function getTransactionTypeAttribute(){
        $types =[
            'E' => ['name' => 'Entrada', 'icon' => 'mdi-logout', 'id' => 'E'],
            'D' => ['name' => 'Salida', 'icon' => 'mdi-logout', 'id' => 'D'],
        ];
        return $types[$this->attributes['transaction_type']] ?? ['name' => 'Desconocido', 'icon' => 'icono-desconocido'];
    }
    public function getPurposeAttribute(){
        $types =[
            'IB' => ['name' => 'Balance inicial',  'id' => 'IB'],
            'D' => ['name' => 'Donación',  'id' => 'D'],
            'A' => ['name' => 'Ajuste', 'id' => 'A'],
            'S' => ['name' => 'Alfanumerico', 'id' => 'S'],
        ];
        return $types[$this->attributes['purpose']] ?? ['name' => 'Desconocido', 'icon' => 'icono-desconocido'];
    }
}
