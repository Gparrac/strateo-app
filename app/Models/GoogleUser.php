<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleUser extends Model
{
    protected $table = 'google_users';
    use HasFactory;
    protected $fillable = ['name', 'email', 'id_account', 'refresh_token', 'access_token','time_expire', 'users_id','users_update_id', 'users_id'];

}
