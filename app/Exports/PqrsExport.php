<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PqrsExport implements FromArray, WithHeadings
{
   
    protected $logs;

    public function headings(): array
    {
        return [
            'Fecha',
            'N. de consecutivo',
            'Persona',
            'Correo electrónico',
            'Tipo de Radicado',
            'Estado',
        ];
    }

    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }

    public function array(): array
    {
        return $this->logs;
    }
}
