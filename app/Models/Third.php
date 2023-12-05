<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Third extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_document',
        'identification',
        'name',
        'surnames',
        'business_name',
        'address',
        'mobile',
        'email',
        'email2',
    ];
    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }
}
