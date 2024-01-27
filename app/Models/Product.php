<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'consecutive',
        'name',
        'description',
        'quantity',
        'measures_id',
        'categories_id',
        'product_code',
        'barcode',
        'photo1',
        'photo2',
        'photo3',
        'users_id',
        'users_update_id'
    ];

    public function category(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot(['users_id', 'users_update_id', 'status']);
    }

    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
