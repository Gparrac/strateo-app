<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description','users_id', 'users_udpate_id', 'status'];

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
        return $this->belongsToMany(Permission::class,'permission_roles')->withPivot('form_id','status');
    }
    public function forms()
    {
        return $this->hasManyThrough(Permission::class, 'permission_roles', 'role_id','form_id');
    }
}
