<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RatingsExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            'Restaurante',
            'Suma de calificacion',
            'Numero de calificaciones',
            'Promedio',
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
