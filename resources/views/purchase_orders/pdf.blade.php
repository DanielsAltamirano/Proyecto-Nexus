<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra #{{ $purchaseOrder->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #7c3aed; padding-bottom: 10px; }
        .logo { font-size: 24px; font-weight: bold; color: #5b21b6; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f3f4f6; color: #374151; }
        .text-right { text-align: right; }
        .total-row th { background-color: #e5e7eb; font-size: 14px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Nexus ERP</div>
        <h2>ORDEN DE COMPRA</h2>
        <p>Folio: OC-{{ str_pad($purchaseOrder->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Datos del Proveedor:</strong><br>
                Nombre: {{ $purchaseOrder->provider->name }}<br>
                Dirección: {{ $purchaseOrder->provider->address ?? 'N/A' }}<br>
                Teléfono: {{ $purchaseOrder->provider->phone ?? 'N/A' }}
            </td>
            <td width="50%">
                <strong>Detalles de la Orden:</strong><br>
                Fecha de Emisión: {{ $purchaseOrder->created_at->format('d/m/Y H:i') }}<br>
                Generada por: {{ $purchaseOrder->user?->name ?? 'N/A' }}<br>
                Estado: {{ strtoupper($purchaseOrder->status) }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Código/SKU</th>
                <th>Descripción</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
            <tr>
                <td>{{ $item->product->sku }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="4" class="text-right">TOTAL DE LA ORDEN</th>
                <th class="text-right">${{ number_format($purchaseOrder->total_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($purchaseOrder->notes)
    <div style="margin-top: 20px; padding: 10px; background-color: #f9fafb; border: 1px solid #e5e7eb;">
        <strong>Notas / Comentarios:</strong><br>
        {{ $purchaseOrder->notes }}
    </div>
    @endif

    <div class="footer">
        <p>Documento generado por Nexus ERP el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
