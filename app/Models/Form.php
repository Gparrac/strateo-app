<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'href',
        'class',
        'icon',
        'table',
        'orden',
        'status',
        'section_id',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
    public function permissions()
    {
        return $this->hasManyThrough(Permission::class, 'permission_roles', 'form_id', 'permission_id');
    }
}
