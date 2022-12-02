<?php

namespace App\Exports;

//use App\Models\RestaurantClient;
//use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromArray, WithHeadings
{
   /*
    public function collection()
    {
        return RestaurantClient::all();
    }
    */

    protected $clients;

    public function headings(): array
    {
        return [
            'id',
            'Nombre',
            'Correo electrónico',
            'Teléfono',
            'Fecha de creación',
        ];
        /*
        return [
            'id',
            'name',
            'email',
            'phone',
            'created',
        ];
        */
    }

    public function __construct(array $clients)
    {
        $this->clients = $clients;
    }

    public function array(): array
    {
        return $this->clients;
    }
}
