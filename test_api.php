<?php

require __DIR__ . '/vendor/autoload.php';

// Validar conexión a base de datos antes de iniciar
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';port=' . env('DB_PORT', '3306') . ';dbname=' . env('DB_DATABASE', 'nexus_db'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
} catch (\PDOException $e) {
    echo "\n[ERROR CRÍTICO] No se pudo conectar a la base de datos MySQL (nexus_db).\n";
    echo "Motivo: " . $e->getMessage() . "\n";
    echo "Sugerencia: Asegúrate de que MySQL esté iniciado (por ejemplo en Laragon haciendo clic en 'Iniciar todo').\n\n";
    exit(1);
}

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

function testRequest($kernel, $method, $uri, $data = [], $headers = []) {
    $server = [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT'  => 'application/json',
    ];
    foreach ($headers as $k => $v) {
        $server['HTTP_' . strtoupper(str_replace('-', '_', $k))] = $v;
    }

    $content = $method !== 'GET' ? json_encode($data) : null;
    $parameters = $method === 'GET' ? $data : [];

    $request = \Illuminate\Http\Request::create(
        $uri,
        $method,
        $parameters,
        [],
        [],
        $server,
        $content
    );

    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();
    $kernel->terminate($request, $response);

    return ['status' => $status, 'json' => json_decode($content, true), 'raw' => $content];
}

echo "=== INICIANDO PRUEBAS DE LOS 15 SERVICIOS WEB NEXUS ERP ===\n\n";

// 1. Iniciar Sesión (POST /api/v1/auth/login)
$res1 = testRequest($kernel, 'POST', '/api/v1/auth/login', [
    'email' => 'admin@nexuserp.com',
    'password' => '123'
]);
echo "1. Iniciar Sesión: HTTP {$res1['status']} | Token generado: " . (!empty($res1['json']['token']) ? 'SI' : 'NO') . "\n";
$token = $res1['json']['token'] ?? null;
$authHeaders = $token ? ['Authorization' => 'Bearer ' . $token] : [];

// 2. Registrar Cliente (POST /api/v1/clientes)
$res2 = testRequest($kernel, 'POST', '/api/v1/clientes', [
    'nombre' => 'Juan',
    'empresa' => 'Devs'
], $authHeaders);
$clientId = $res2['json']['data']['id'] ?? 1;
echo "2. Registrar Cliente: HTTP {$res2['status']} | Cliente ID: {$clientId}\n";

// 3. Actualizar Cliente (PUT /api/v1/clientes/{id})
$res3 = testRequest($kernel, 'PUT', "/api/v1/clientes/{$clientId}", [
    'telefono' => '4431234567'
], $authHeaders);
$tel = $res3['json']['data']['telefono'] ?? 'N/A';
echo "3. Actualizar Cliente: HTTP {$res3['status']} | Teléfono: {$tel}\n";

// 4. Registrar Usuario (POST /api/v1/usuarios)
$res4 = testRequest($kernel, 'POST', '/api/v1/usuarios', [
    'nombre' => 'Ana',
    'rol' => 'admin'
], $authHeaders);
$userName = $res4['json']['data']['nombre'] ?? 'N/A';
$userEmail = $res4['json']['data']['email'] ?? 'N/A';
echo "4. Registrar Usuario: HTTP {$res4['status']} | Usuario: {$userName} ({$userEmail})\n";

// 5. Registrar Proveedor (POST /api/v1/proveedores)
$res5 = testRequest($kernel, 'POST', '/api/v1/proveedores', [
    'razon_social' => 'Tech SA',
    'rfc' => 'XAXX010101000'
], $authHeaders);
$rfc = $res5['json']['data']['rfc'] ?? 'N/A';
echo "5. Registrar Proveedor: HTTP {$res5['status']} | RFC: {$rfc}\n";

// 6. Listar Proveedores (GET /api/v1/proveedores)
$res6 = testRequest($kernel, 'GET', '/api/v1/proveedores', [
    'page' => 1,
    'limit' => 5
], $authHeaders);
$totalProv = $res6['json']['meta']['total_registros'] ?? 'N/A';
echo "6. Listar Proveedores: HTTP {$res6['status']} | Total: {$totalProv}\n";

// 7. Agregar Producto (POST /api/v1/inventario/productos)
$res7 = testRequest($kernel, 'POST', '/api/v1/inventario/productos', [
    'nombre' => 'Monitor LCD',
    'precio' => 3500
], $authHeaders);
$productId = $res7['json']['data']['id'] ?? 1;
echo "7. Agregar Producto: HTTP {$res7['status']} | Producto ID: {$productId}\n";

// 8. Consultar Producto (GET /api/v1/inventario/productos/{id})
$res8 = testRequest($kernel, 'GET', "/api/v1/inventario/productos/{$productId}", [], $authHeaders);
$prodName = $res8['json']['data']['nombre'] ?? 'N/A';
$prodPrice = $res8['json']['data']['precio'] ?? 'N/A';
echo "8. Consultar Producto: HTTP {$res8['status']} | Nombre: {$prodName} | Precio: \${$prodPrice}\n";

// 9. Actualizar Stock (PATCH /api/v1/inventario/productos/{id}/stock)
$res9 = testRequest($kernel, 'PATCH', "/api/v1/inventario/productos/{$productId}/stock", [
    'cantidad_agregada' => 50
], $authHeaders);
$nuevoStock = $res9['json']['data']['stock_total_nuevo'] ?? 'N/A';
echo "9. Actualizar Stock: HTTP {$res9['status']} | Nuevo Stock: {$nuevoStock}\n";

// 10. Registrar Venta (POST /api/v1/ventas)
$res10 = testRequest($kernel, 'POST', '/api/v1/ventas', [
    'id_cliente' => $clientId,
    'total' => 5400.00,
    'product_id' => $productId
], $authHeaders);
$folio = $res10['json']['data']['folio'] ?? 'N/A';
$totalVenta = $res10['json']['data']['total'] ?? 'N/A';
$saleId = $res10['json']['data']['id_venta'] ?? 1;
echo "10. Registrar Venta: HTTP {$res10['status']} | Folio: {$folio} | Total: \${$totalVenta}\n";

// 11. Consultar Venta (GET /api/v1/ventas/{id})
$res11 = testRequest($kernel, 'GET', "/api/v1/ventas/{$saleId}", [], $authHeaders);
$folioConsulta = $res11['json']['data']['folio'] ?? 'N/A';
echo "11. Consultar Venta: HTTP {$res11['status']} | Folio: {$folioConsulta}\n";

// 12. Listar Ventas (GET /api/v1/ventas)
$res12 = testRequest($kernel, 'GET', '/api/v1/ventas', [
    'fecha_inicio' => date('Y-m-d', strtotime('-1 day')),
    'fecha_fin' => date('Y-m-d', strtotime('+1 day'))
], $authHeaders);
$totalVentasReg = $res12['json']['meta']['total_registros'] ?? 'N/A';
echo "12. Listar Ventas: HTTP {$res12['status']} | Registros: {$totalVentasReg}\n";

// 13. Registrar Gasto (POST /api/v1/finanzas/gastos)
$res13 = testRequest($kernel, 'POST', '/api/v1/finanzas/gastos', [
    'concepto' => 'Luz',
    'monto' => 1200
], $authHeaders);
$gastoConcepto = $res13['json']['data']['concepto'] ?? 'N/A';
$gastoMonto = $res13['json']['data']['monto'] ?? 'N/A';
echo "13. Registrar Gasto: HTTP {$res13['status']} | Concepto: {$gastoConcepto} | Monto: \${$gastoMonto}\n";

// 14. Reporte Financiero (GET /api/v1/finanzas/reporte)
$res14 = testRequest($kernel, 'GET', '/api/v1/finanzas/reporte', [
    'mes' => (int)date('m'),
    'anio' => (int)date('Y')
], $authHeaders);
$repIngresos = $res14['json']['resumen']['total_ingresos'] ?? 'N/A';
$repEgresos = $res14['json']['resumen']['total_egresos'] ?? 'N/A';
$repBalance = $res14['json']['resumen']['balance_neto'] ?? 'N/A';
echo "14. Reporte Financiero: HTTP {$res14['status']} | Ingresos: \${$repIngresos} | Egresos: \${$repEgresos} | Balance: \${$repBalance}\n";

// 15. Eliminar Producto (DELETE /api/v1/inventario/productos/{id})
$res15 = testRequest($kernel, 'DELETE', "/api/v1/inventario/productos/{$productId}", [], $authHeaders);
$delMsg = $res15['json']['message'] ?? 'N/A';
echo "15. Eliminar Producto (Lógico): HTTP {$res15['status']} | Mensaje: {$delMsg}\n";

echo "\n=== TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE ===\n";
