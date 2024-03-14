<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DynamicService extends Model
{
    use HasFactory;
    protected $fillable = ['supplier_id', 'status', 'employee_id', 'field_id', 'users_id', 'users_update_id', 'service_id', 'path_info'];

    public function service() : BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function fields() : BelongsToMany
    {
        return $this->belongsToMany(Field::class,'fields_dynamic_services', 'dynamic_service_id', 'field_id')->withPivot(['path_info']);
    }
}
