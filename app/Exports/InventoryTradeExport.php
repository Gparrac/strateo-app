<?php

namespace App\Exports;

use App\Models\InventoryTrade;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
class InventoryTradeExport implements FromCollection,WithHeadings, WithMapping, WithEvents, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return InventoryTrade::join('inventories_inventory_trades','inventory_trades.id','inventories_inventory_trades.inventory_trade_id')
        ->join('inventories','inventories_inventory_trades.inventory_id','inventory_id')->join('products','inventories.product_id','products.id')
        ->join('warehouses','warehouses.id','inventories.warehouse_id')->join('brands','products.brand_id','brands.id')->join('measures','measures.id','products.measure_id')
        ->join('cities','warehouses.city_id','cities.id')
        ->select(
            'inventory_trades.id',
            'inventory_trades.transaction_date',
            'inventory_trades.created_at',
            'inventory_trades.updated_at',
            'inventory_trades.transaction_type',
            'inventory_trades.purpose',
            'inventories_inventory_trades.amount',
            'inventories_inventory_trades.cost',
            'inventories.stock',
            'products.name',
            'products.consecutive',
            'products.size',
            'measures.symbol',
            'warehouses.address',
            'cities.name as city'
        )->get();
    }
    public function headings(): array
    {
        return [
            '#id',
            'Fecha de transacción',
            'Creación',
            'Ultima actualización',
            'Tipo de transacción',
            'Proposito',
            'Cantidad',
            'Costo',
            'Stock en inventario',
            'Producto',
            'Consecutivo',
            'Tamaño presentación',
            'Unidad',
            'Dirección Bodega',
            'Ciudad de Bodega'
        ];
    }
    public function map($bill): array
    {
        return[
            $bill->id,
            $bill->transaction_date,
            Date::dateTimeToExcel($bill->created_at),
            Date::dateTimeToExcel($bill->updated_at),
            $bill->transaction_type['name'],
            $bill->purpose['name'],
            $bill->amount,
            $bill->cost,
            $bill->stock,
            $bill->name,
            $bill->consecutive,
            $bill->size,
            $bill->symbol,
            $bill->address,
            $bill->city
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
        'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
