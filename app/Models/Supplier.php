<?php

namespace App\Models;

use App\Casts\FileCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $casts = [
        'commercial_registry_file' => FileCast::class,
        'rut_file' => FileCast::class
    ];
    use HasFactory;
    protected $fillable = ['commercial_registry', 'commercial_registry_file','rut_file', 'note', 'status', 'third_id', 'users_id', 'users_update_id'];
    public function services() : BelongsToMany {
        return $this->belongsToMany(Service::class, 'dynamic_services', 'supplier_id', 'service_id');
    }
    public function dynamicServices() : HasMany {
        return $this->hasMany(DynamicService::class);
    }
    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class);
    }
}
