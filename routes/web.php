<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\PromoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/tienda', function () {
    return view('tienda');
})->name('tienda');

Route::get('/nosotros', function () {
    return view('nosotros');
})->name('nosotros');

Route::get('/sucursales', function () {
    $branches = \App\Models\Branch::where('name', 'not like', '%Bodega%')->get();
    return view('sucursales', compact('branches'));
})->name('sucursales');

Route::get('/vacantes', [App\Http\Controllers\PublicVacancyController::class, 'index'])->name('vacancies.public.index');
Route::post('/vacantes/{vacancy}/apply', [App\Http\Controllers\PublicVacancyController::class, 'apply'])->name('vacancies.public.apply');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Ruta para ver el ticket (accesible para clientes, el controlador verifica propiedad)
    Route::get('/sales/{receipt_id}/ticket', [SaleController::class, 'showTicket'])->name('sales.ticket');

    // Rutas Compartidas con Doctor
    Route::middleware(['role:admin|superadmin|Personal de Mostrador|compras|Doctor'])->group(function () {
        Route::get('/pos', [SaleController::class, 'create'])->name('pos');
        Route::get('/dashboard', function() { return redirect()->route('pos'); })->name('dashboard');

        // Rutas de Solicitudes de Producto (y Ajustes Unificados)
        Route::post('product-requests/approve-from-bodega/{productRequest}', [\App\Http\Controllers\ProductRequestController::class, 'approveFromBodega'])->name('product_requests.approveFromBodega');
        Route::get('product-requests/create-purchase-order/{productRequest}', [\App\Http\Controllers\ProductRequestController::class, 'createPurchaseOrder'])->name('product_requests.createPurchaseOrder');
        Route::resource('product-requests', \App\Http\Controllers\ProductRequestController::class)->names('product_requests')->except(['edit', 'update', 'destroy']);
    });

    // Rutas exclusivas de Servicios (Catálogo de servicios)
    Route::middleware(['role:admin|superadmin|Doctor'])->group(function () {
        Route::resource('services', App\Http\Controllers\ServiceController::class)->except(['create', 'show', 'edit']);
        Route::post('/services-requests/{serviceRequest}/approve', [App\Http\Controllers\ServiceController::class, 'approveEdit'])->name('services.approve_edit');
        Route::post('/services-requests/{serviceRequest}/reject', [App\Http\Controllers\ServiceController::class, 'rejectEdit'])->name('services.reject_edit');
    });
    // Rutas Administrativas y Operativas (Bloqueadas para 'cliente' y 'Doctor')
    Route::middleware(['role:admin|superadmin|Personal de Mostrador|compras'])->group(function () {
        // Rutas de Lotes - CRUD
        Route::patch('/batches/{batch}/inline-update', [BatchController::class, 'inlineUpdate'])->name('batches.inlineUpdate');
        Route::get('/batches/export', [BatchController::class, 'export'])->name('batches.export');
        Route::post('/batches/import', [BatchController::class, 'import'])->name('batches.import');
        Route::get('/batches/{batch}/history', [BatchController::class, 'historyApi'])->name('batches.historyApi');
        Route::post('/batches/{batch}/request-edit', [BatchController::class, 'requestEdit'])->name('batches.request_edit');
        Route::post('/batches-requests/{batchEditRequest}/approve', [BatchController::class, 'approveEdit'])->name('batches.approve_edit');
        Route::post('/batches-requests/{batchEditRequest}/reject', [BatchController::class, 'rejectEdit'])->name('batches.reject_edit');
        Route::delete('/batches-requests/{batchEditRequest}/cancel', [BatchController::class, 'cancelRequest'])->name('batches.cancel_request');
        Route::get('/batches-requests/{batchEditRequest}', [BatchController::class, 'showRequest'])->name('batches.requests.show');
        Route::get('/batches/deletions', [BatchController::class, 'deletions'])->middleware('role:admin|superadmin')->name('batches.deletions');
        Route::resource('batches', BatchController::class);
        
        // Rutas de Caja (Corte de Caja)
        Route::post('/cash-register/check', [CashRegisterController::class, 'checkSession'])->name('cash_register.check');
        Route::post('/cash-register/open', [CashRegisterController::class, 'openSession'])->name('cash_register.open');
        Route::post('/cash-register/close', [CashRegisterController::class, 'closeSession'])->name('cash_register.close');
        Route::get('/cash-register/ticket/{id}', [CashRegisterController::class, 'showTicket'])->name('cash_register.ticket');
        Route::post('/cash-register/log-audit', [CashRegisterController::class, 'logBreakdownOpened'])->name('cash_register.log_audit');
        Route::post('/cash-register/daily-expense', [CashRegisterController::class, 'registerDailyExpense'])->name('cash_register.daily_expense');
        Route::get('/cash-register/session-summary', [CashRegisterController::class, 'getSessionSummary'])->name('cash_register.session_summary');
        Route::post('/cash-register/confirm-doctor-payment', [CashRegisterController::class, 'confirmDoctorPayment'])->name('cash_register.confirm_doctor_payment');
        Route::get('/expenses/api-list', [ExpenseController::class, 'apiList'])->name('expenses.apiList');

        // Rutas de Ventas (El punto de venta principal está en dashboard)
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');

        // Búsqueda de cliente por barcode (Para POS)
        Route::get('/users/by-barcode/{barcode}', [App\Http\Controllers\UserController::class, 'getByBarcode'])->name('users.byBarcode');
        // Rutas de Traspasos
        Route::get('transfers/request', [\App\Http\Controllers\TransferController::class, 'requestTransfer'])->name('transfers.request');
        Route::post('transfers/request', [\App\Http\Controllers\TransferController::class, 'storeRequest'])->name('transfers.storeRequest');
        Route::get('transfers/{transfer}/print', [\App\Http\Controllers\TransferController::class, 'print'])->name('transfers.print');
        Route::resource('transfers', \App\Http\Controllers\TransferController::class)->only(['index', 'create', 'store', 'show', 'update']);
        // Rutas de Órdenes de Compra
        Route::get('purchase-orders/{purchase_order}/pdf', [\App\Http\Controllers\PurchaseOrderController::class, 'downloadPdf'])->name('purchase-orders.pdf');
        Route::get('purchase-orders/{purchase_order}/xml', [\App\Http\Controllers\PurchaseOrderController::class, 'downloadXml'])->name('purchase-orders.xml');
        Route::get('purchase-orders/{purchase_order}/receive', [\App\Http\Controllers\PurchaseOrderController::class, 'receiveForm'])->name('purchase-orders.receiveForm');
        Route::post('purchase-orders/{purchase_order}/receive', [\App\Http\Controllers\PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);
        

    });

    // Rutas de Comunidad y Staff (Solo Admin y Superadmin)
    Route::middleware(['role:admin|superadmin'])->group(function () {
        Route::get('/users/staff', [App\Http\Controllers\UserController::class, 'indexStaff'])->name('users.staff');
        Route::post('/users/staff', [App\Http\Controllers\UserController::class, 'storeStaff'])->name('users.staff.store');
        Route::get('/users/clients', [App\Http\Controllers\UserController::class, 'indexClients'])->name('users.clients');
        Route::post('/users/points-value', [App\Http\Controllers\UserController::class, 'updatePointsValue'])->name('users.updatePointsValue');
        Route::put('/users/{user}/staff', [App\Http\Controllers\UserController::class, 'updateStaff'])->name('users.updateStaff');
        Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

        // Módulo de Vacantes (Admin)
        Route::resource('admin/vacancies', App\Http\Controllers\AdminVacancyController::class)->names([
            'index' => 'admin.vacancies.index',
            'create' => 'admin.vacancies.create',
            'store' => 'admin.vacancies.store',
            'edit' => 'admin.vacancies.edit',
            'update' => 'admin.vacancies.update',
            'destroy' => 'admin.vacancies.destroy',
        ]);

        Route::get('admin/vacancies/{vacancy}/applications-json', [App\Http\Controllers\AdminVacancyController::class, 'applicationsJson'])->name('admin.vacancies.applications.json');
        Route::get('admin/vacancies/applications/{application}/cv', [App\Http\Controllers\AdminVacancyController::class, 'viewCv'])->name('admin.vacancies.applications.cv');
        Route::patch('admin/vacancies/applications/{application}/status', [App\Http\Controllers\AdminVacancyController::class, 'updateStatus'])->name('admin.vacancies.applications.status');

        // Módulo de Metas de Venta
        Route::get('/metas', [App\Http\Controllers\MetasController::class, 'index'])->name('metas.index');
        Route::post('/metas', [App\Http\Controllers\MetasController::class, 'update'])->name('metas.update');

        // Módulo de Turnos (Auditoría)
        Route::get('/turnos', [CashRegisterController::class, 'turnosIndex'])->name('turnos.index');

        // Módulo de Promos
        Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
        Route::post('/promos/update-settings', [PromoController::class, 'updateSettings'])->name('promos.updateSettings');
        Route::post('/promos/update-receta-settings', [PromoController::class, 'updateRecetaSettings'])->name('promos.updateRecetaSettings');
        Route::get('/promos/generic-products', [PromoController::class, 'genericProducts'])->name('promos.genericProducts');
        Route::post('/promos/update-4x3-settings', [PromoController::class, 'updatePromo4x3Settings'])->name('promos.updatePromo4x3Settings');
        Route::get('/promos/4x3-products', [PromoController::class, 'searchProducts4x3'])->name('promos.searchProducts4x3');
        Route::post('/promos/4x3-products/{product}', [PromoController::class, 'toggleProduct4x3'])->name('promos.toggleProduct4x3');
        Route::post('/promos/update-volumen-settings', [PromoController::class, 'updatePromoVolumenSettings'])->name('promos.updatePromoVolumenSettings');
        Route::get('/promos/volumen-products', [PromoController::class, 'searchProductsVolumen'])->name('promos.searchProductsVolumen');
        Route::post('/promos/volumen-products/{product}', [PromoController::class, 'toggleProductVolumen'])->name('promos.toggleProductVolumen');

        // Módulo de Gastos Operativos (Catálogo)
        Route::resource('expenses', ExpenseController::class)->except(['create', 'show', 'edit']);

        // Módulo de Reportes
        Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar-ventas', [\App\Http\Controllers\ReporteController::class, 'exportarVentas'])->name('reportes.exportar-ventas');
        Route::get('/reportes/exportar-caducidades', [\App\Http\Controllers\ReporteController::class, 'exportarCaducidades'])->name('reportes.exportar-caducidades');
        Route::get('/reportes/exportar-inventario', [\App\Http\Controllers\ReporteController::class, 'exportarInventario'])->name('reportes.exportar-inventario');
    });

    // Rutas de Catálogo (Categorías, Productos, Proveedores)
    Route::middleware(['role:admin|superadmin|compras'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::get('/products/create-general', [ProductController::class, 'createGeneral'])->name('products.create_general');
        Route::resource('products', ProductController::class);
        Route::resource('providers', ProviderController::class);
        
        // Rutas AJAX para Lotes/Medicamentos
        Route::post('/products/api-store', [ProductController::class, 'storeApi'])->name('products.storeApi');
        Route::post('/categories/api-store', [CategoryController::class, 'storeApi'])->name('categories.storeApi');
        Route::post('/providers/api-store', [ProviderController::class, 'storeApi'])->name('providers.storeApi');
    });

    // Rutas exclusivas para Superadmin (Crear/Editar/Eliminar sucursales)
    Route::middleware(['role:superadmin'])->group(function () {
        Route::post('/branches', [App\Http\Controllers\BranchController::class, 'store'])->name('branches.store');
        Route::put('/branches/{branch}', [App\Http\Controllers\BranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{branch}', [App\Http\Controllers\BranchController::class, 'destroy'])->name('branches.destroy');
        Route::post('/cash-register/{id}/manual-close', [CashRegisterController::class, 'manualClose'])->name('cash_register.manual_close');
    });
});

require __DIR__.'/auth.php';
