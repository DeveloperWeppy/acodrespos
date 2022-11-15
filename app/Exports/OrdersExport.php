<?php

namespace App\Exports;

use App\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromArray, WithHeadings
{
    protected $orders;

    public function headings(): array
    {
        return [
            'Nº de Orden',
            'Nombre del Restaurante',
            'Restaurante id',
            'Fecha de la Orden',
            'Último estado del Pedido',
            'Nombre del Cliente',
            'Cliente',
            'Dirección',
            'Direccion id',
            'Nombre del Domiciliario',
            'Conductor id',
            'Metodo de Pago',
            'Metodo Pago id',
            'restaurant_fee_percent',
            'order_fee',
            'restaurant_static_fee',
            'platform_fee',
            'processor_fee',
            'Costo de Domicilio',
            'Valor Neto con Impoconsumo',
            'Valor de Impoconsumo',
            'Valor Neto',
            'Total de la Orden',
            'Descuento'
        ];
        /*
        return [
            'order_id',
            'restaurant_name',
            'restaurant_id',
            'created',
            'last_status',
            'client_name',
            'client_id',
            'table_name',
            'table_id',
            'area_name',
            'area_id',
            'address',
            'address_id',
            'driver_name',
            'driver_id',
            'order_value',
            'order_delivery',
            'order_total',
            'payment_method',
            'srtipe_payment_id',
            'order_fee',
            'restaurant_fee',
            'restaurant_static_fee',
            'vat',
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
