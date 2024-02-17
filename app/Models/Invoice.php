<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['client_id','note','seller_id','further_discount','status','sale_type', 'users_update_id', 'users_id'];

    public function planment() : HasOne
    {
        return $this->hasOne(Planment::class);
    }
    public function seller() : BelongsTo
    {
        return $this->belongsTo(User::class,'seller_id');
    }
    public function products() : BelongsToMany
    {
        return $this->belongsToMany(Product::class,'products_invoices','invoice_id','product_id')->withPivot(['amount','cost','discount']);
    }

}
