<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciiu extends Model
{
    use HasFactory;
    protected $table = 'code_ciiu';
    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    public function thirds(){
        return $this->hasMany(Third::class,'code_ciiu_id');
    }
    public function secondaryThirds(){
        return $this->belongsToMany(Third::class, 'code_ciiu_thirds', 'code_ciiu_id', 'thirds_id');
    }
}
