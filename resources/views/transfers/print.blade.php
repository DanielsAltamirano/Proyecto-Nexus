<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guía de Traspaso #TR-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .box {
            border: 1px solid #000;
            padding: 10px;
        }
        .box-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 5px;
            padding-bottom: 3px;
        }
        .box-content {
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-right { text-align: right; }
        .print-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        @media print {
            .print-btn { display: none; }
            .container { border: none; padding: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Imprimir Guía</button>

    <div class="container">
        <div class="header">
            <div>
                <img src="{{ asset('img/nexusbanner.jpeg') }}" style="height: 45px; filter: grayscale(100%) contrast(1.2); margin-bottom: 15px;" alt="Nexus ERP">
                <h1 class="title">Guía de Traspaso</h1>
                <p style="margin: 5px 0 0 0; font-size: 18px;"><strong>#TR-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</strong></p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0;"><strong>Fecha de Creación:</strong> <br> {{ $transfer->created_at->format('d M Y, h:i A') }}</p>
                <p style="margin: 5px 0 0 0;"><strong>Estado Actual:</strong> <br> EN TRÁNSITO</p>
            </div>
        </div>

        <div class="info-grid">
            <div class="box">
                <div class="box-title">Origen (Remitente)</div>
                <div class="box-content">{{ $transfer->fromBranch?->name ?? 'N/A' }}</div>
                <p style="margin: 5px 0 0 0; font-weight: normal; font-size: 12px;">Generado por: {{ $transfer->creator?->name ?? 'N/A' }}</p>
            </div>
            <div class="box">
                <div class="box-title">Destino (Receptor)</div>
                <div class="box-content">{{ $transfer->toBranch?->name ?? 'N/A' }}</div>
                <p style="margin: 5px 0 0 0; font-weight: normal; font-size: 12px;">Para entrega en sucursal</p>
            </div>
        </div>

        @if($transfer->notes)
            <div class="box" style="margin-bottom: 20px;">
                <div class="box-title">Notas / Instrucciones</div>
                <p style="margin: 5px 0 0 0;">{{ $transfer->notes }}</p>
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>SKU / Lote</th>
                    <th>Descripción de Producto</th>
                    <th class="text-center">Cant. Solicitada</th>
                    <th class="text-center">Cant. Enviada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfer->items as $index => $item)
                    @php
                        $prod = $item->product ?? ($item->batch ? $item->batch->product : null);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>SKU:</strong> {{ $prod ? $prod->sku : 'N/A' }} <br>
                            <span style="font-size: 12px;"><strong>Lote:</strong> {{ $item->batch ? $item->batch->batch_number : 'Pendiente' }}</span>
                        </td>
                        <td>{{ $prod ? $prod->name : 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity_requested }}</td>
                        <td class="text-center"><strong>{{ $item->quantity_sent }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</body>
</html>
