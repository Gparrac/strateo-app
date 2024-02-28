<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class,'services_employees', 'service_id', 'employee_id')->withPivot(['path_info']);
    }
}
