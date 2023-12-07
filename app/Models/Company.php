<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'path_logo',
        'header',
        'footer',
    ];

    //Relationship
    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class);
    }
}
