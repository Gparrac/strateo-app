<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tax extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'acronym', 'type', 'default_percent','status','users_update_id','users_id','context'];

    public function products() :BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_taxes','tax_id', 'product_id')->withPivot('percent');
    }
    public function invoices() :BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'products_taxes','tax_id', 'invoice_id')->withPivot('percent');
    }
}
