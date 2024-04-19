<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaxValue extends Model
{
    use HasFactory;
    protected $table = 'tax_values';
    protected $fillable = ['percent','users_id', 'users_update_id'];
    public function taxes() : BelongsToMany
    {
        return $this->belongsToMany(Tax::class,'tax_values_taxes','tax_value_id','tax_id');
    }
}
