<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Third extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_document',
        'identification',
        'verification_id', //NIT COMPANY
        'names',
        'surnames',
        'business_name',
        'address',
        'mobile',
        'email',
        'email2',
        'postal_code',
        'city_id',
        'users_id',
        'users_update_id'
    ];

    //Relationship
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
    public function ciiu(): BelongsTo
    {
        return $this->belongsTo(Ciiu::class,'id');
    }
}
