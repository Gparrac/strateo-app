<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status',
        'users_id',
        'users_update_id'
    ];

    public function products(): BelongsToMany 
    {
        return $this->belongsToMany(Product::class, 'categories_products')->withPivot(['users_id', 'users_update_id', 'status']);
    }
}
