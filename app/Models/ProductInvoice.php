<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductInvoice extends Model
{
    use HasFactory;
    protected $table = "products_invoices";
    protected $fillable = ['product_id', 'planment_id', 'cost', 'discount', 'status', 'users_id', 'users_udate_id'];

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function invoice() : BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function taxes() : BelongsToMany
    {
        return $this->belongsToMany(Tax::class,'products_taxes','product_invoice_id','tax_id')->withPivot(['percent']);
    }
    public function warehouse() : BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }


}
