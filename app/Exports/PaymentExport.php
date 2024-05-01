<?php

namespace App\Exports;

use App\Models\EmployeePlanment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
class PaymentExport implements FromCollection,WithHeadings, WithMapping, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $ep =  EmployeePlanment::with([
            'paymentMethod:id,name', 'charges:id,name',
            'employee' => function ($query) {
                $query->with('third:id,names,surnames,business_name,identification,type_document');
            }, 'planment' => function ($query) {
                $query->with(['invoice' => function ($query) {
                    $query->with(['client' => function ($query) {
                        $query->with('third:id,names,surnames,business_name,identification,type_document');
                        $query->select('clients.id', 'clients.third_id');
                    }]);
                    $query->select('invoices.id', 'invoices.client_id');
                }]);
                $query->select('planments.id', 'planments.invoice_id','planments.start_date', 'planments.end_date','stage');
            }
        ])->select('employees_planments.id', 'employees_planments.employee_id', 'employees_planments.planment_id', 'employees_planments.payment_method_id', 'employees_planments.salary', 'employees_planments.settled', 'employees_planments.reference','employees_planments.updated_at')->get();
        // $ep = $ep->map(function($query){
        //     return [
        //         'id' => $query['id'],
        //         'name' => $query['employee']['third']['fullname'],
        //         'document'=> $query['employee']['third']['fullid'],
        //         'reference' => $query['reference'],
        //         'paymentMethod' => $query['paymentMethod'] ? $query['paymentMethod']['name'] : '',
        //         'salary' => $query['salary'],
        //         'charges' => implode( ",", $query->charges->map(function($item){
        //             return $item->name;
        //         })->toArray()) ?? '',
        //         'customer' => $query['planment']['invoice']['client']['third']['fullname'],
        //         'customerId' =>$query['planment']['invoice']['client']['third']['fullid'],
        //         'planmentStartDate' => $query['planment']['start_date'],
        //         'planmentId' => $query['planment']['id'],
        //         'end_date' => $query['planment']['end_date'],
        //         'settled' => $query['settled'] == 0 ? 'Pendiente' : 'Liquidado',
        //         'updated_at' => $query['updated_at'],
        //     ];

        // });
        return $ep;
    }
    public function headings(): array
    {
        return [
            '#id',
            'Nombre',
            'Identificación',
            'referencia de pago',
            'Metodo de pago',
            'Salario',
            'Cargos',
            'Cliente',
            'Cliente ID',
            'Producto',
            'Inicio del evento',
            'Fin del evento',
            'Estado pago',
            'Ultima actualización',
        ];
    }
    public function map($bill): array
    {
        return[
                 $bill->id,
                 $bill->employee['third']['fullname'],
                 $bill->employee['third']['fullid'],
                 $bill->reference,
                 $bill->paymentMethod ? $bill->paymentMethod['name'] : '',
                 $bill->salary,
                 implode( ",", $bill->charges->map(function($item){
                    return $item->name;
                })->toArray()) ?? '',
                 $bill->planment['invoice']['client']['third']['fullname'],
                $bill->planment['invoice']['client']['third']['fullid'],
                $bill->planment['id'],
                 $bill->planment['start_date'],
                 $bill->planment['end_date'],
                 $bill->settled == 0 ? 'Pendiente' : 'Liquidado',
                 $bill->updated_at,
        ];
    }
    public function registerEvents(): array
    {
        return
        [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A1:Q1')->applyFromArray([
                    'font'=>[
                        'bold' => true
                    ]
                ]);
            }
        ];
    }
    public function columnFormats(): array
    {
        return [
        'K' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        'L' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        'M' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
