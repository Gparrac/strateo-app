<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status',
        'users_id',
        'users_update_id'
    ];

    public function products(): HasMany 
    {
        return $this->hasMany(Product::class);
    }
}
