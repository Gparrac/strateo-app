<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Third extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_document',
        'identification',
        'verification_id', //NIT COMPANY
        'name',
        'surnames',
        'business_name',
        'address',
        'mobile',
        'email',
        'email2',
    ];

    //Relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }
}
