<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0mm;
        }
        * {
            box-sizing: border-box;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'Consolas', 'Lucida Console', 'Courier New', monospace;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            color: #000;
            -webkit-font-smoothing: antialiased;
        }
        .ticket-container {
            width: 72mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 3mm 1mm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 800; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mt-2 { margin-top: 8px; }
        .border-top { border-top: 1.5px dashed #000; padding-top: 6px; }
        .border-bottom { border-bottom: 1.5px dashed #000; padding-bottom: 6px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 3px 0; font-size: 13px; }
        .header-title { font-size: 18px; font-weight: 800; margin-bottom: 3px; letter-spacing: 0.5px; }
        .sub-header { font-size: 11px; font-weight: bold; }
        
        .btn-action {
            display: inline-block;
            padding: 10px 18px;
            margin: 10px 4px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            border: 1.5px solid #000;
            background: #fff;
            text-decoration: none;
        }
        .btn-print {
            background-color: #000;
            color: #fff !important;
        }

        @media screen {
            body {
                background-color: #f1f5f9;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                min-height: 100vh;
                padding: 28px 12px;
                margin: 0;
            }
            .ticket-container {
                width: 76mm;
                max-width: 100%;
                background-color: #ffffff;
                padding: 6mm 4.5mm;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                margin: auto 0;
            }
            .btn-action {
                transition: all 0.2s;
            }
            .btn-action:hover {
                opacity: 0.9;
                transform: translateY(-1px);
            }
            .btn-action:active {
                transform: translateY(0);
            }
        }
        
        @media print {
            body { 
                margin: 0 !important; 
                padding: 0 !important; 
                background-color: #fff !important;
                display: block !important;
            }
            .ticket-container {
                width: 72mm !important;
                margin: 0 !important;
                padding: 1mm 1mm !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                background-color: #fff !important;
            }
            .no-print { 
                display: none !important; 
            }
        }
    </style>
</head>
<body>

    <div class="ticket-container">
        
        <!-- CABECERA -->
        <div class="text-center mb-2">
            <div class="header-title">NEXUS ERP</div>
            <div class="sub-header">Sucursal: {{ $session->branch->name ?? 'N/A' }}</div>
        </div>

        <div class="border-top border-bottom text-center font-bold" style="font-size: 14px; letter-spacing: 0.5px;">
            CORTE DE CAJA (CIERRE DE TURNO)
        </div>

        @if(str_starts_with($session->notes ?? '', 'CIERRE MANUAL'))
        <div class="border-bottom text-center font-bold" style="font-size: 14px; padding: 4px 0; letter-spacing: 1px;">
            *** CIERRE MANUAL ***
        </div>
        @endif

        <!-- INFO CAJERO Y HORAS -->
        <div class="mb-2">
            <table>
                <tr>
                    <td class="font-bold">Cajero:</td>
                    <td class="text-right font-bold">{{ $session->user?->name ?? 'Usuario no disponible' }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Apertura:</td>
                    <td class="text-right">{{ $session->start_time->format('d/m/y H:i') }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Cierre:</td>
                    <td class="text-right">{{ $session->end_time ? $session->end_time->format('d/m/y H:i') : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- VENTAS DEL TURNO -->
        <div class="border-top mb-2">
            <div class="font-bold text-center mb-1" style="font-size: 13px; letter-spacing: 0.5px;">VENTAS DEL TURNO</div>
            <table>
                <tr class="font-bold" style="font-size: 15px;">
                    <td>TOTAL VENDIDO:</td>
                    <td class="text-right">${{ number_format($session->total_sales, 2) }}</td>
                </tr>
                <tr><td colspan="2" style="padding: 2px 0;"></td></tr>
                <tr>
                    <td>- Efectivo:</td>
                    <td class="text-right">${{ number_format($session->cash_sales, 2) }}</td>
                </tr>
                <tr>
                    <td>- Tarjeta:</td>
                    <td class="text-right">${{ number_format($session->card_sales, 2) }}</td>
                </tr>
                <tr>
                    <td>- Transferencia:</td>
                    <td class="text-right">${{ number_format($session->transfer_sales, 2) }}</td>
                </tr>
            </table>
        </div>

        @php
            $totalExpenses = $session->dailyExpenses->sum('amount');
            $docPayment = $session->doctor_payment_amount ?? 0;
            // El pago al doctor sale de caja solo hasta el efectivo disponible; el resto queda pendiente.
            $availableCash = max(($session->starting_cash ?? 0) + ($session->cash_sales ?? 0) - $totalExpenses, 0);
            $docCashPayment = min($docPayment, $availableCash);
            $docPending = max($docPayment - $docCashPayment, 0);
        @endphp

        <!-- DEDUCCIONES -->
        <div class="border-top border-bottom mb-2" style="padding: 6px 0;">
            <div class="font-bold text-center mb-1" style="font-size: 13px; letter-spacing: 0.5px;">DEDUCCIONES (EFECTIVO)</div>
            <table>
                <tr class="font-bold">
                    <td>Gastos Operativos:</td>
                    <td class="text-right">-${{ number_format($totalExpenses, 2) }}</td>
                </tr>
                <tr class="font-bold">
                    <td>Pago a Doctor:</td>
                    <td class="text-right">-${{ number_format($docCashPayment, 2) }}</td>
                </tr>
                @if($docPending > 0)
                <tr>
                    <td colspan="2" class="font-bold text-center" style="padding-top: 4px;">*** PAGO A DOCTOR INCOMPLETO ***</td>
                </tr>
                <tr>
                    <td>Total a pagar:</td>
                    <td class="text-right">${{ number_format($docPayment, 2) }}</td>
                </tr>
                <tr>
                    <td>Pagado en caja:</td>
                    <td class="text-right">${{ number_format($docCashPayment, 2) }}</td>
                </tr>
                <tr class="font-bold">
                    <td>Doctor pendiente:</td>
                    <td class="text-right">${{ number_format($docPending, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 10px;">Sin efectivo suficiente en caja. El saldo no se incluye en el cierre.</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- CIERRE FÍSICO -->
        <div class="mb-2">
            <div class="font-bold text-center mb-1" style="font-size: 13px; letter-spacing: 0.5px;">CIERRE FÍSICO (CAJA)</div>
            <table>
                <tr>
                    <td>Fondo Inicial:</td>
                    <td class="text-right">${{ number_format($session->starting_cash, 2) }}</td>
                </tr>
                <tr>
                    <td>Ventas Efectivo (+):</td>
                    <td class="text-right">${{ number_format($session->cash_sales, 2) }}</td>
                </tr>
                <tr>
                    <td>Gastos & Doc (-):</td>
                    <td class="text-right">-${{ number_format($totalExpenses + $docCashPayment, 2) }}</td>
                </tr>
                <tr><td colspan="2" class="border-top" style="padding: 2px 0;"></td></tr>
                <tr class="font-bold" style="font-size: 15px;">
                    <td>DEBERÍA HABER:</td>
                    <td class="text-right">${{ number_format($session->expected_cash, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Cajero Contó:</td>
                    <td class="text-right font-bold">${{ number_format($session->actual_cash, 2) }}</td>
                </tr>
                <tr class="font-bold border-top" style="font-size: 14px;">
                    <td>Diferencia:</td>
                    <td class="text-right font-bold">
                        @if($session->difference > 0)
                            +${{ number_format($session->difference, 2) }} (SOBRANTE)
                        @elseif($session->difference < 0)
                            -${{ number_format(abs($session->difference), 2) }} (FALTANTE)
                        @else
                            $0.00 (EXACTO)
                        @endif
                    </td>
                </tr>
                @php
                    $tExpected = $session->expected_cash ?? 0;
                    $tStarting = $session->starting_cash ?? 0;
                    $tDiff = $session->difference ?? 0;
                    $tSobrante = $tDiff > 0 ? $tDiff : 0;
                    $tLapicera = max(($tExpected - $tStarting) + $tSobrante, 0);
                @endphp
                <tr class="border-top" style="font-size: 14px;">
                    <td class="font-bold">Dinero en la Lapicera:</td>
                    <td class="text-right font-bold">${{ number_format($tLapicera, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($session->notes)
        <div class="border-top mb-2 mt-2">
            <div class="font-bold mb-1">Notas:</div>
            <div>{{ $session->notes }}</div>
        </div>
        @endif

        <div class="text-center mt-2 border-top sub-header" style="padding-top: 20px;">
            ________________________________<br>
            Firma Cajero
        </div>

        <div class="text-center mt-2 no-print" style="margin-top: 20px;">
            <button onclick="window.print()" class="btn-action btn-print">🖨️ Imprimir</button>
            <button onclick="window.close()" class="btn-action">Cerrar</button>
        </div>

    </div>

    <!-- SCRIPT DE AUTO IMPRESIÓN -->
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
