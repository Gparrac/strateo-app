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
        'users_id',
        'type_content',
        'users_update_id',
        'brand_id',
        'measure_id',
        'status',
        'size',
        'supply',
        'tracing',
        'cost'
    ];
    public function getTypeAttribute(){
        $types =[
            'T' => ['name' => 'Tangible',  'id' => 'T'],
            'I' => ['name' => 'Intangible',  'id' => 'I'],

        ];
        return $types[$this->attributes['type']] ?? ['name' => 'Desconocido'];
    }
    public function getTypeContentAttribute(){
        $types =[
            'E' => ['name' => 'Evento',  'id' => 'E'],
            'C' => ['name' => 'Consumible',  'id' => 'C'],
            'L' => ['name' => 'Lugar',  'id' => 'L']
        ];
        return $types[$this->attributes['type_content']] ?? ['name' => 'Desconocido'];
    }
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
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'products_products','child_product_id', 'parent_product_id')->withPivot(['amount']);
    }
    public function subproducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'products_products','parent_product_id', 'child_product_id')->withPivot(['amount']);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class,'products_taxes','product_id','tax_id')->withPivot('percent');
    }

    public function planments(): BelongsToMany
    {
        return $this->belongsToMany(Planment::class,'products_planments', 'product_id', 'planment_id')->withPivot(['cost', 'descount', 'amount']);
    }
    public function subproductPlanments(): HasMany
    {
        return $this->hasMany(SubproductPlanment::class, 'product_id');
    }
    public function productPlanments(): HasMany
    {
        return $this->hasMany(ProductPlanment::class, 'product_id');
    }
    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class,'products_invoices', 'product_id', 'invoices_id')->withPivot(['cost', 'descount', 'amount']);
    }
    public function librettoActivities(): BelongsToMany
    {
        return $this->belongsToMany(LibrettoActivity::class, 'libretto_activities_products', 'product_id', 'libretto_activity_id');
    }

}
