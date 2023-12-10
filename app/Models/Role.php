<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**__________________________________________
     *                RELATIONSHIP
     * ___________________________________________
     */
    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }
    public function permissions():BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withPivot('form_id','status');
    }
}
