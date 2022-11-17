<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderByDayExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            'Nº de Orden',
            'Fecha de la Orden',
            'Atendido por',
            'Total de la Orden',
            'Propina',
            'Metodo de pago',
            'Tipo de pedido',
        ];
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

