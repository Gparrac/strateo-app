<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['type_contract', 'hire_date', 'end_date_contract', 'rut_file', 'resume_file', 'third_id', 'status'];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class,'employees_services','employee_id','service_id');
    }
    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class,'fields_employees','employee_id','field_id');
    }
}
