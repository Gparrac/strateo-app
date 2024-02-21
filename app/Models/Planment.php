<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Planment extends Model
{
    use HasFactory;

    protected $fillable = ['start_date', 'end_date', 'stage', 'status', 'users_id', 'users_update_id', 'invoice_id', 'pay_off'];

    public function invoice() : BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function employees() : BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employees_planments', 'planment_id', 'employee_id')->withPivot('salary');
    }
    public function furtherProducts() : BelongsToMany
    {
        return $this->belongsToMany(Product::class,'further_products_planments','planment_id', 'product_id')->withPivot(['amount','cost','discount']);
    }
    public function productPlanments() : BelongsToMany
    {
        return $this->hasMany(ProductPlanment::class, 'planment_id')->withPivot(['amount','cost','discount']);
    }
}
