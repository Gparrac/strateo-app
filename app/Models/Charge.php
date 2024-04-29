<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Charge extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description','users_id','status','users_update_id'];
    public function employeePlanments() : BelongsToMany
    {
        return $this->belongsToMany(EmployeePlanment::class,'charges_employee_planments','charge_id', 'employee_planment_id');
    }
}
