<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'third_id',
        'city_id',
        'status',
        'users_id',
        'users_update_id',
        'address'
    ];

    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
