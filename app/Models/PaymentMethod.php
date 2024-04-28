<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'payment_methods';
    protected $fillable = ['name', 'description', 'status', 'users_id', 'users_update_id'];
    public function employeePlanments() : HasMany
    {
        return $this->hasMany(EmployeePlanment::class);
    }
    public function employees() : BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employees_payment_methods','payment_method_id', 'employee_id')->withPivot(['reference']);
    }
}
