<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentasResumenSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected float $totalVentas;
    protected float $totalGastos;
    protected float $gananciaNeta;

    public function __construct(float $totalVentas, float $totalGastos, float $gananciaNeta)
    {
        $this->totalVentas  = $totalVentas;
        $this->totalGastos  = $totalGastos;
        $this->gananciaNeta = $gananciaNeta;
    }

    public function collection()
    {
        return collect([
            ['Total de Ventas', '$' . number_format($this->totalVentas, 2)],
            ['Gastos Operativos', '$' . number_format($this->totalGastos, 2)],
            ['Ganancia Neta', '$' . number_format($this->gananciaNeta, 2)],
        ]);
    }

    public function headings(): array
    {
        return ['Concepto', 'Monto'];
    }

    public function title(): string { return 'Resumen Financiero'; }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF166534']]],
            4 => ['font' => ['bold' => true, 'color' => ['argb' => 'FF166534']]],
        ];
    }
}