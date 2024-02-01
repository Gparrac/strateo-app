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
        'size'
    ];
    public function inventory(): HasMany
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
            'SE' => ['name' => 'Servicio',  'id' => 'SE'],
            'PR' => ['name' => 'Producto',  'id' => 'PR'],
            'PL' => ['name' => 'Lugar',  'id' => 'PL'],
        ];
        return $types[$this->attributes['type']] ?? ['name' => 'Desconocido'];
    }
    public function getTypeContentAttribute(){
        $types =[
            '0' => ['name' => 'Insumo',  'id' => '0'],
            '1' => ['name' => 'Consumible',  'id' => '1'],
            '2' => ['name' => 'Venta',  'id' => '2'],
        ];
        return $types[$this->attributes['type_content']] ?? ['name' => 'Desconocido', 'id' => 'nn'];
    }
}
