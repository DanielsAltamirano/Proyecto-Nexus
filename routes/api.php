<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\SaleController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\ProviderController;
use App\Http\Controllers\Api\V1\InventoryController;

/*
|--------------------------------------------------------------------------
| Nexus ERP (TIMaster Solutions) - Web Services API v1
|--------------------------------------------------------------------------
|
| Responsables:
| - Daniels John Altamirano Ramírez
| - Juan Manuel Martínez Martínez
| - Gabriel Cázares Castañeda
| - Cristian Alexander Pérez Reyes
|
*/

Route::prefix('v1')->group(function () {

    // -------------------------------------------------------------
    // Módulo de Autenticación
    // -------------------------------------------------------------
    // Servicio 8: Iniciar Sesión (Daniels J. Altamirano)
    Route::post('/auth/login', [AuthController::class, 'login']);

    // -------------------------------------------------------------
    // Módulo CRM - Clientes
    // -------------------------------------------------------------
    // Servicio 1: Registrar Cliente (Daniels J. Altamirano)
    Route::post('/clientes', [ClientController::class, 'store']);
    // Servicio 2: Actualizar Cliente (Juan M. Martinez)
    Route::put('/clientes/{id}', [ClientController::class, 'update']);

    // -------------------------------------------------------------
    // Módulo de Ventas
    // -------------------------------------------------------------
    // Servicio 3: Registrar Venta (Gabriel Cazares)
    Route::post('/ventas', [SaleController::class, 'store']);
    // Servicio 4: Consultar Venta (Cristian A. Perez)
    Route::get('/ventas/{id}', [SaleController::class, 'show']);
    // Servicio 5: Listar Ventas (Daniels J. Altamirano)
    Route::get('/ventas', [SaleController::class, 'index']);

    // -------------------------------------------------------------
    // Módulo de Finanzas
    // -------------------------------------------------------------
    // Servicio 6: Registrar Gasto (Juan M. Martinez)
    Route::post('/finanzas/gastos', [FinanceController::class, 'storeExpense']);
    // Servicio 7: Reporte Financiero (Gabriel Cazares)
    Route::get('/finanzas/reporte', [FinanceController::class, 'report']);

    // -------------------------------------------------------------
    // Módulo de Usuarios
    // -------------------------------------------------------------
    // Servicio 9: Registrar Usuario (Juan M. Martinez)
    Route::post('/usuarios', [UserController::class, 'store']);

    // -------------------------------------------------------------
    // Módulo de Proveedores
    // -------------------------------------------------------------
    // Servicio 10: Registrar Proveedor (Gabriel Cazares)
    Route::post('/proveedores', [ProviderController::class, 'store']);
    // Servicio 11: Listar Proveedores (Cristian A. Perez)
    Route::get('/proveedores', [ProviderController::class, 'index']);

    // -------------------------------------------------------------
    // Módulo de Inventario
    // -------------------------------------------------------------
    // Servicio 12: Agregar Producto (Daniels J. Altamirano)
    Route::post('/inventario/productos', [InventoryController::class, 'store']);
    // Servicio 13: Consultar Producto (Juan M. Martinez)
    Route::get('/inventario/productos/{id}', [InventoryController::class, 'show']);
    // Servicio 14: Actualizar Stock (Gabriel Cazares)
    Route::patch('/inventario/productos/{id}/stock', [InventoryController::class, 'updateStock']);
    // Servicio 15: Eliminar Producto [Borrado lógico] (Cristian A. Perez)
    Route::delete('/inventario/productos/{id}', [InventoryController::class, 'destroy']);
});
