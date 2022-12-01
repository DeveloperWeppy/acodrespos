<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsCountExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            'N° de item',
            'Nombre',
            'Cantidad',
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




