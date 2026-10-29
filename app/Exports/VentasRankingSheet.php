<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentasRankingSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $rows;
    public function __construct(array $rows) { $this->rows = collect($rows); }
    public function collection() { return $this->rows; }
    public function headings(): array {
        return ['#', 'Cajero', 'Turnos Trabajados', 'Total Vendido ($)'];
    }
    public function title(): string { return 'Ranking Cajeros'; }
    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF166534']]]];
    }
}