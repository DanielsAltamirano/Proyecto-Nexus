<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VentasExport implements WithMultipleSheets
{
    protected array $rows;
    protected array $topProductos;
    protected array $rankingCajeros;
    protected float $totalVentas;
    protected float $totalGastos;
    protected float $gananciaNeta;

    public function __construct(array $rows, array $topProductos, array $rankingCajeros, float $totalVentas, float $totalGastos, float $gananciaNeta)
    {
        $this->rows           = $rows;
        $this->topProductos   = $topProductos;
        $this->rankingCajeros = $rankingCajeros;
        $this->totalVentas    = $totalVentas;
        $this->totalGastos    = $totalGastos;
        $this->gananciaNeta   = $gananciaNeta;
    }

    public function sheets(): array
    {
        return [
            new VentasDetalleSheet($this->rows),
            new VentasTopProductosSheet($this->topProductos),
            new VentasRankingSheet($this->rankingCajeros),
            new VentasResumenSheet($this->totalVentas, $this->totalGastos, $this->gananciaNeta),
        ];
    }
}