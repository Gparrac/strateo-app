<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'consecutive',
        'name',
        'description',
        'quantity',
        'categories_id',
        'product_code',
        'barcode',
        'photo1',
        'photo2',
        'photo3',
        'users_id',
        'type_content',
        'users_update_id',
        'brand_id',
        'measure_id',
        'status',
        'size',
        'supply'
    ];
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class,'categories_products','product_id','category_id')->withPivot(['users_id', 'users_update_id', 'status']);
    }

    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function childrenProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'products_products','parent_product_id', 'child_product_id')->withPivot(['amount']);
    }
    public function getTypeAttribute(){
        $types =[
            'T' => ['name' => 'Tangible',  'id' => 'T'],
            'I' => ['name' => 'Intangible',  'id' => 'I'],

        ];
        return $types[$this->attributes['type']] ?? ['name' => 'Desconocido'];
    }
    public function getTypeContentAttribute(){
        $types =[
            'E' => ['name' => 'EVENTO',  'id' => 'E'],
            'C' => ['name' => 'CONSUMIBLE',  'id' => 'C'],
            'L' => ['name' => 'LUGAR',  'id' => 'L']
        ];
        return $types[$this->attributes['type_content']] ?? ['name' => 'Desconocido'];
    }
    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class,'products_taxes','product_id','tax_id')->withPivot('cost');
    }

    public function planments(): BelongsToMany
    {
        return $this->belongsToMany(Planment::class,'products_planments', 'product_id', 'planment_id')->withPivot(['cost', 'descount', 'amount']);
    }
    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class,'products_invoices', 'product_id', 'invoices_id')->withPivot(['cost', 'descount', 'amount']);
    }

}
