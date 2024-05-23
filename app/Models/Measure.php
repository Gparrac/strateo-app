<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Measure extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'symbol',
        'status',
        'users_id',
        'users_update_id'
    ];
    protected $appends = ['fulltype'];

    public function getFulltypeAttribute()
    {
        switch ($this->type) {
            case 'TI':
                $type = ['id' => 'TI','color'=>'pink', 'name' => 'Tiempo'];
                break;
            case 'WE':
                $type = ['id' => 'WE','color'=>'purple', 'name' => 'Peso'];
                break;
            case 'LE':
                $type = ['id' => 'LE','color'=>'green', 'name' => 'Tamaño'];
                break;
            default:
                $type = ['id' => 'VO','color'=>'blue', 'name' => 'Volumen'];
                break;
        }
        return $type;
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
