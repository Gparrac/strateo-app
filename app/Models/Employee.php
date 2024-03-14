<?php

namespace App\Models;

use App\Casts\FileCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $casts = [
        'resume_file' => FileCast::class,
        'rut_file' => FileCast::class,
    ];
    protected $fillable = ['type_contract', 'hire_date', 'end_date_contract', 'rut_file', 'resume_file', 'third_id', 'status', 'users_id', 'users_update_id'];

    public function services() : BelongsToMany {
        return $this->belongsToMany(Service::class, 'dynamic_services', 'supplier_id', 'service_id');
    }
    public function dynamicServices() : HasMany {
        return $this->hasMany(DynamicService::class,'employee_id');
    }

    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class);
    }
    public function getTypeContractAttribute(){
        $types =[
            'TF' => ['name' => 'Termino fijo', 'id' => 'TF'],
            'TI' => ['name' => 'Termino indefinido', 'id' => 'TI'],
            'OL' => ['name' => 'Obra o labor', 'id' => 'OL'],
            'PS' => ['name' => 'Prestación de servicios', 'id' => 'PS'],
            'CA' => ['name' => 'Contrato de aprendizaje', 'id' => 'CA'],
            'OT' => ['name' => 'Ocasional de trabajo', 'id' => 'OT']
        ];
        return $types[$this->attributes['type_contract']] ?? ['name' => 'Desconocido', 'icon' => 'icono-desconocido'];
    }
}
