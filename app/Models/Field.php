<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Field extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type', 'length', 'status', 'users_id', 'users_update_id'];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class,'fields_services', 'fields_id', 'services_id')->withPivot(['users_id', 'users_update_id', 'required']);
    }
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class,'suppliers_fields', 'fields_id', 'suppliers_id')->withPivot(['path_info']);
    }
    public function getTypeAttribute(){
        $types =[
            'F' => ['name' => 'Archivo', 'icon' => 'mdi-file-send', 'id' => 'F'],
            'T' => ['name' => 'Texto', 'icon' => 'mdi-format-font', 'id' => 'T'],
            'N' => ['name' => 'Número', 'icon' => 'mdi-numeric', 'id' => 'N'],
            'A' => ['name' => 'Alfanumerico', 'icon' => 'mdi-format-header-pound', 'id' => 'A'],
        ];
        return $types[$this->attributes['type']] ?? ['name' => 'Desconocido', 'icon' => 'icono-desconocido'];
    }
}
