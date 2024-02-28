<?php

namespace App\Models;

use App\Casts\FileCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $casts = [
        'commercial_registry_file' => FileCast::class,
        'rut_file' => FileCast::class
    ];
    use HasFactory;
    protected $fillable = ['commercial_registry', 'commercial_registry_file','rut_file', 'note', 'status', 'third_id', 'users_id', 'users_update_id'];
    public function fields() : BelongsToMany {
        return $this->belongsToMany(Field::class, 'suppliers_fields', 'suppliers_id', 'fields_id')->withPivot(['path_info']);
    }
    public function services() : BelongsToMany {
        return $this->belongsToMany(Service::class, 'suppliers_services', 'suppliers_id', 'services_id')->withPivot(['status']);
    }
    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class);
    }
}
