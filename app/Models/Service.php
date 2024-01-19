<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'users_id',
        'users_update_id'
    ];

    public function fields(): BelongsToMany
    {
        return $this->BelongsToMany(Field::class, 'fields_services', 'services_id', 'fields_id')->withPivot(['users_id', 'users_update_id', 'required']);
    }
    public function suppliers(): BelongsToMany
    {
        return $this->BelongsToMany(Supplier::class, 'suppliers_services', 'services_id', 'suppliers_id')->withPivot(['users_id', 'users_update_id', 'required']);
    }
}
