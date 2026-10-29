<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta {{ $receipt_id }}</title>
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
        .bold { font-weight: 800; }
        .uppercase { text-transform: uppercase; }
        
        .header {
            margin-bottom: 8px;
            border-bottom: 1.5px dashed #000;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 13px;
        }
        
        .divider {
            border-top: 1.5px dashed #000;
            margin: 8px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 3px 0;
            font-size: 13px;
        }
        th {
            font-weight: 800;
            border-bottom: 1.5px dashed #000;
        }
        .col-qty { width: 14%; text-align: center; }
        .col-desc { width: 56%; text-align: left; padding-left: 4px; word-break: break-word; }
        .col-price { width: 30%; text-align: right; }
        
        .totals {
            margin-top: 8px;
            border-top: 1.5px dashed #000;
            padding-top: 8px;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1.5px dashed #000;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            background-color: #000;
            color: #fff !important;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
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
            .btn-print {
                transition: background-color 0.2s, transform 0.1s;
            }
            .btn-print:hover {
                background-color: #1e293b;
                transform: translateY(-1px);
            }
            .btn-print:active {
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
        
        <!-- BOTÓN NO IMPRIMIBLE -->
        <button onclick="window.print()" class="btn-print no-print">🖨️ IMPRIMIR TICKET</button>
        <br class="no-print">

        <!-- CABECERA -->
        <div class="header text-center">
            <h1 class="uppercase">Farmacias CAMUR</h1>
            <div>Ticket: <span class="bold">{{ $receipt_id }}</span></div>
            <div>Fecha: {{ $date }}</div>
        </div>

        <!-- INFO -->
        <div class="info-row">
            <span>Atendió:</span>
            <span>{{ $cashier }}</span>
        </div>
        <div class="info-row">
            <span>Sucursal:</span>
            <span>{{ $branch }}</span>
        </div>

        <div class="divider"></div>

        <!-- ARTICULOS -->
        <table>
            <thead>
                <tr>
                    <th class="col-qty">CANT</th>
                    <th class="col-desc">DESCRIPCIÓN</th>
                    <th class="col-price">TOTAL</th>
                </tr>
            </thead>
            <tbody>
            @php 
                $total_ahorrado = 0; 
            @endphp
            @foreach($items as $item)
                @php
                    $ahorroItem = 0;
                    if($item->original_unit_price && ($item->original_unit_price * $item->quantity) > $item->total_price) {
                        $ahorroItem = ($item->original_unit_price * $item->quantity) - $item->total_price;
                        $total_ahorrado += $ahorroItem;
                    }
                @endphp
                <tr>
                    <td class="col-qty">{{ $item->quantity }}</td>
                    <td class="col-desc">
                        {{ Str::limit($item->product ? $item->product->name : ($item->service ? $item->service->name : 'N/A'), 20) }}
                        @if($item->discount_pct)
                            <br><small style="font-size:11px; font-weight:bold;">*Promo -{{ $item->discount_pct }}%</small>
                        @elseif($ahorroItem > 0 && !$item->discount_pct)
                            @if($item->product && $item->product->is_promo_volumen)
                                <br><small style="font-size:11px; font-weight:bold;">*Promo x Volumen</small>
                            @else
                                <br><small style="font-size:11px; font-weight:bold;">*Promo 4x3</small>
                            @endif
                        @endif
                    </td>
                    <td class="col-price">
                        @if($ahorroItem > 0)
                            <span style="text-decoration:line-through; font-size:11px;">${{ number_format($item->original_unit_price * $item->quantity, 2) }}</span><br>
                            <small style="font-size:10px; font-weight:bold;">Ahorro: ${{ number_format($ahorroItem, 2) }}</small><br>
                        @endif
                        ${{ number_format($item->total_price, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTALES -->
        <div class="totals">
            @if($total_ahorrado > 0)
            <div class="info-row bold" style="font-size: 13px; margin-bottom: 6px;">
                <span>USTED AHORRÓ:</span>
                <span>${{ number_format($total_ahorrado, 2) }}</span>
            </div>
            @endif
            <div class="info-row bold" style="font-size: 16px;">
                <span>TOTAL A PAGAR:</span>
                <span>${{ number_format($total, 2) }}</span>
            </div>
            
            <div class="divider"></div>
            
            <div class="info-row">
                <span>MÉTODO DE PAGO:</span>
                <span class="uppercase">{{ $payment_method }}</span>
            </div>
            
            @if($payment_method === 'mixto')
            <div class="divider"></div>
            @if((float)$mixed_cash > 0 || ((float)$mixed_card == 0 && (float)$mixed_transfer == 0))
            <div class="info-row">
                <span>-> PAGO EN EFECTIVO:</span>
                <span>${{ number_format($mixed_cash, 2) }}</span>
            </div>
            @endif
            @if((float)$mixed_card > 0 || ((float)$mixed_cash == 0 && (float)$mixed_card == 0 && (float)$mixed_transfer == 0))
            <div class="info-row">
                <span>-> PAGO CON TARJETA:</span>
                <span>${{ number_format($mixed_card, 2) }}</span>
            </div>
            @endif
            @if((float)$mixed_transfer > 0 || ((float)$mixed_cash == 0 && (float)$mixed_card == 0 && (float)$mixed_transfer == 0))
            <div class="info-row">
                <span>-> PAGO CON TRANSFERENCIA:</span>
                <span>${{ number_format($mixed_transfer, 2) }}</span>
            </div>
            @endif
            
            @if((float)$amount_paid > 0 && (float)$mixed_cash > 0)
            <div class="divider"></div>
            <div class="info-row">
                <span>EFECTIVO RECIBIDO:</span>
                <span>${{ number_format($amount_paid, 2) }}</span>
            </div>
            <div class="info-row">
                <span>SU CAMBIO:</span>
                <span>${{ number_format($change_amount, 2) }}</span>
            </div>
            @endif
            
            @elseif($payment_method === 'efectivo' && $amount_paid)
            <div class="info-row">
                <span>EFECTIVO RECIBIDO:</span>
                <span>${{ number_format($amount_paid, 2) }}</span>
            </div>
            <div class="info-row">
                <span>SU CAMBIO:</span>
                <span>${{ number_format($change_amount, 2) }}</span>
            </div>    
            @endif
        </div>

        <!-- PIE DE TICKET -->
        <div class="footer">
            <p>¡Gracias por su compra!</p>
            <p>Este comprobante no es válido como factura.</p>
        </div>
        
    </div>

    <!-- SCRIPT DE AUTO IMPRESIÓN -->
    <script>
        window.onload = function() {
            // Uncomment to auto-print when loaded in popup
            // window.print();
        }
    </script>
</body>
</html>
