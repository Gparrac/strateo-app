<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaxProduct extends Model
{
    use HasFactory;
    protected $fillable = ['porcent', 'status', 'tax_id', 'users_update_id', 'users_id', 'product_id', 'product_invoice_id', 'product_planment_id', 'further_product_planment_id'];

    public function taxes() : BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }



}
