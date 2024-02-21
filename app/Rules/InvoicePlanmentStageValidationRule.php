<?php

namespace App\Rules;

use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Planment;
use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InvoicePlanmentStageValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $planment;
    protected $request;


    public function __construct($request)
    {
        $this->planment = Invoice::find($request['invoice_id'])->planment;;
        $this->request = $request;

    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->request->state == 'QUO') {
            if ($this->request['stage'] == 'CON' && $this->request['payOff'] <= 0) {
                $fail('Para confirmar este evento es necesario hacer un abono.');
            }else{
                $this->request->merge(['stage' => 'CAN']);
            }
        } elseif ( in_array($this->request->state, ['CON','REA', 'FIN'])) {
            if ($this->request['stage'] == 'QUO') {
                $this->request->merge(['stage' => 'CAN']);
            } elseif ($this->request['stage'] == 'REA') {
                //validate products' stock available
                foreach ($this->request['products'] as $event) {
                    foreach ($event['children_products'] as  $product) {
                        $inventory = Product::find($product['product_id'])->inventories()->where('warehouse_id', $product['warehouse_id'])->first();
                        if ($inventory && $inventory->stock  < $product['amount']) {
                            $fail('Uno de los productos no cuenta con inventario suficiente');
                    }
                }
                }
                //valitade employees with schedule to set off
                $employees = $this->request['employees']->pluck('employee_id')->toArray();
                $startDate = $this->request['start_date'];
                $endDate = $this->request['end_date'];
                    $overlocatedEvent = Planment::whereHas('employees', function ($query) use ($employees, $startDate, $endDate) {
                        $query->whereIn('employee_id', $employees);
                    })->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate])
                              ->orWhereBetween('end_date', [$startDate, $endDate])
                              ->orWhere(function ($query) use ($startDate, $endDate) {
                                  $query->where('start_date', '<=', $startDate)
                                        ->where('end_date', '>=', $endDate);
                              });
                    })->exists();
                    if($overlocatedEvent){
                        $fail('Uno de los empleados ya presenta eventos para la fecha.');
                    }
                }else{
                    $this->request->merge(['stage' => 'CAN']);
                }
            }
    }
}
