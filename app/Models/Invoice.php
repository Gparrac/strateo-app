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
    protected $fillable = ['client_id','note','seller_id','status','sale_type', 'users_update_id', 'users_id'];

    public function getSaleTypeAttribute(){
        $types =[
            'P' => ['name' => 'Venta directa',  'id' => 'P'],
            'E' => ['name' => 'Planeación',  'id' => 'E'],

        ];
        return $types[$this->attributes['sale_type']] ?? ['name' => 'Desconocido'];
    }
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
    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class,'client_id');
    }
    public function taxes() : BelongsToMany
    {
        return $this->belongsToMany(Tax::class,'products_taxes','invoice_id','tax_id')->withPivot('percent');
    }

}
