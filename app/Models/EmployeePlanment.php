<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeePlanment extends Model
{
    use HasFactory;
    protected $table  = 'employees_planments';
    protected $fillable = ['payment_method_id', 'reference','settled'];
    public function paymentMethod() : BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function  planment() : BelongsTo
    {
        return $this->belongsTo(Planment::class);
    }
    public function charges() : BelongsToMany
    {
        return $this->belongsToMany(Charge::class,'charges_employee_planments');
    }
    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
