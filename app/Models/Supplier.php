<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = ['commercial_registry', 'commercial_registry_file','rut_file', 'note', 'status', 'third_id', 'users_id', 'users_update_id'];
    public function fields() : BelongsToMany {
        return $this->belongsToMany(Service::class, 'suppliers_fields', 'suppliers_id', 'services_id')->withPivot(['status']);
    }
    public function services() : BelongsToMany {
        return $this->belongsToMany(Service::class, 'suppliers_services', 'suppliers_id', 'services_id')->withPivot(['status']);
    }
}
