<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

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
        'code_ciiu_id',
        'users_id',
        'users_update_id'
    ];
    public function getNamesAttribute()
    {
        $fullname = $this->attributes['names'] . ' '.  ($this->attributes['surnames'] ?? '');
        return $this->attributes['business_name'] ?? $fullname;
    }
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
        return $this->belongsTo(Ciiu::class,'code_ciiu_id');
    }
    public function secondaryCiius(): BelongsToMany
    {
        return $this->belongsToMany(Ciiu::class, 'code_ciiu_thirds', 'thirds_id', 'code_ciiu_id')->withPivot('status');
    }
    public function warehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class);
    }
}
