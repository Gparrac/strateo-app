<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\FileCast;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'commercial_registry',
        'commercial_registry_file',
        'rut_file',
        'legal_representative_name',
        'legal_representative_id',
        'note',
        'status',
        'third_id',
        'created_at',
        'updated_at',
        'users_id',
        'users_update_id'
    ];

    protected $casts = [
        'commercial_registry_file' => FileCast::class,
        'rut_file' => FileCast::class,
    ];

    public function third(): BelongsTo
    {
        return $this->belongsTo(Third::class);
    }
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
