<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogsExport implements FromArray, WithHeadings
{
   
    protected $logs;

    public function headings(): array
    {
        return [
            'N.',
            'Fecha',
            'Usuario',
            'Modulo',
            'Submodulo',
            'Evento',
            'Detalle del evento',
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
