<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'role_id',
        'third_id',
        'users_id',
        'users_update_id',
        'status'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**__________________________________________
     *                RELATIONSHIP
     * ___________________________________________
     */
    public function role():BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function third():BelongsTo
    {
        return $this->belongsTo(Third::class);
    }
    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class,'office_users','office_users_id','office_id')->withPivot('status');
    }
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'seller_id');
    }
    public function scopeActiveOffices($query){
        return $query->with('offices', function($subquery){
            $subquery->where('office_users.status','A');
            $subquery->select('offices.id','offices.name');
        });
    }
    public function scopeActiveRole($query){
        return $query->with('role', function($subquery){
            $subquery->where('roles.status','A');
            $subquery->select('roles.id','roles.name');
        });
    }
}
