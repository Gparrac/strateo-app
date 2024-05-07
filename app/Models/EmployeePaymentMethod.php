<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'employees_payment_methods';
    protected $fillable = ['id', 'employee_id', 'payment_method_id', 'reference', 'users_id', 'users_update_id', 'status'];
}
