<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HourOrderExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            'N°  de orden',
            'Estado',
            'Nombre Cliente',
            'Metodo',
            'Fecha de creación',
            'Fecha de finalización',
            'Duración',
        ];
        /*
        return [
            'order_id',
            'last_status',
            'client_name',
            'method',
            'date_initial',
            'date_end',
            'time',
        ];
        */
    }

    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function array(): array
    {
        return $this->orders;
    }
}


