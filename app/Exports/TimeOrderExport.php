<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TimeOrderExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            'order_id',
            'last_status',
            'client_name',
            'method',
            'date_initial',
            'date_end',
            'time',
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

