<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Office extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public function Users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'office_users','office_id','office_users_id')->withPivot('status');
    }
}
