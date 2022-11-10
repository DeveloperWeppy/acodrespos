<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinancesExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            'Nº de Orden',
            'Nombre del Restaurante',
            //'restaurant_id',
            'Fecha de la Orden',
            'Último estado del Pedido',
            'Nombre del Cliente',
            //'client_id',
            'Dirección',
            //'address_id',
            'Nombre del Domiciliario',
            //'driver_id',
            'Metodo de Pago',
            //'srtipe_payment_id',
            //'restaurant_fee_percent',
            //'order_fee',
            //'restaurant_static_fee',
            //'platform_fee',
            //'processor_fee',
            'Costo de Domicilio',
            'Valor Neto con Impoconsumo',
            'Valor de Impoconsumo',
            'Valor Neto',
            'Total de la Orden',
            'Descuento'
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
