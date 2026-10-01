<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuSubcategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ScholarController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SystemLogController;

Auth::routes();

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/unauthorized', function () {
    return view('unauthorized');
})->name('unauthorized');

Route::post('/password-reset/request', [PasswordResetRequestController::class, 'requestReset'])
    ->name('password.request.submit');

Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => view('dashboard'))->name('dashboard')->middleware('permission:view_dashboard');

    Route::prefix('inventory')->group(function () {
        Route::get('/', fn() => view('inventory.index'))->middleware('permission:manage_inventory')->name('inventory.index');
        Route::get('/summary', [InventoryController::class, 'getSummaryData']);

        Route::get('/products/export-pdf', [ProductController::class, 'exportPDF'])->name('products.export-pdf');
        Route::get('/products/export-csv', [ProductController::class, 'exportCSV'])->name('products.export-csv');
        Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');

        Route::get('/products', [ProductController::class, 'index'])->name('inventory.products')->middleware('permission:manage_inventory');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::get('/getproducts', [InventoryController::class, 'getProducts'])->name('inventory.getproducts');
        Route::get('/products/code/{code}', [InventoryController::class, 'getProductByCode'])->name('products.getByCode');

        Route::get('/stock', [InventoryController::class, 'index'])->name('inventory.stock')->middleware('permission:manage_inventory');
        Route::post('/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock.in');
        Route::post('/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock.out');
        Route::post('/stock-adjust', [InventoryController::class, 'stockAdjust'])->name('inventory.stock.adjust');
        Route::get('/stock/{id}/data', [InventoryController::class, 'getProductData']);
        Route::get('/stock/{id}/history', [InventoryController::class, 'getStockHistory'])->name('inventory.stock.history');
        Route::get('/stock-trends', [InventoryController::class, 'getStockTrends'])->name('inventory.stock-trends');

        Route::get('/export/pdf', [InventoryController::class, 'exportPDF'])->name('inventory.export.pdf');
        Route::get('/get-product-by-code/{code}', [App\Http\Controllers\InventoryController::class, 'getProductByCode']);

        Route::post('/update-stock', [InventoryController::class, 'updateStock']);
        Route::get('/get-stock-levels/{product_code}', [InventoryController::class, 'getStockLevels'])
            ->name('inventory.get-stock-levels');


        Route::get('/logs', [InventoryController::class, 'showLogs'])->name('inventory.logs')->middleware('permission:manage_inventory');
        Route::get('/logs/data', [InventoryController::class, 'getLogsData'])->name('inventory.logs.data');

        Route::get('/logs/export-pdf', [InventoryController::class, 'exportLogsPDF'])->name('inventory.logs.export-pdf');
        Route::get('/logs/export-excel', [InventoryController::class, 'exportLogsExcel'])->name('inventory.logs.export-excel');
        Route::get('/logs/export-csv', [InventoryController::class, 'exportLogsCSV'])->name('inventory.logs.export-csv');

        Route::get('/logs/summary', [InventoryController::class, 'getLogsSummary'])->name('inventory.logs.summary');

        Route::get('/analytics/export', [InventoryController::class, 'exportAnalyticsReport'])
            ->name('inventory.analytics.export');
        Route::post('/reports/export', [InventoryController::class, 'exportInventoryReport'])
            ->name('inventory.reports.export');
        Route::get('/reports/preview', [InventoryController::class, 'previewInventoryReport'])
            ->name('inventory.reports.preview');
    });

    Route::prefix('menu')->middleware(['auth'])->group(function () {
        Route::get('/archived', [MenuController::class, 'getArchivedMenus'])->name('menu.archived');
        Route::get('/', [MenuController::class, 'index'])->name('menu.index')->middleware('permission:manage_menu');
        Route::post('/categories', [MenuCategoryController::class, 'store'])->name('menu.categories.store');
        Route::get('/categories', [MenuCategoryController::class, 'index'])->name('menu.categories.index');
        Route::get('/load-menu', [MenuController::class, 'loadMenu'])->name('menu.load-menu')->middleware('permission:manage_menu');
        Route::put('/categories/{id}', [MenuCategoryController::class, 'update'])->name('menu.categories.update');

        Route::post('/subcategories', [MenuSubcategoryController::class, 'store'])->name('menu.subcategories.store');
        Route::get('/categories/{categoryId}/subcategories', [MenuSubcategoryController::class, 'getByCategory'])->name('menu.subcategories.by-category');
        Route::put('/subcategories/{id}', [MenuSubcategoryController::class, 'update'])->name('menu.subcategories.update');

        Route::get('/dashboard-stats', [MenuController::class, 'getDashboardStats'])->name('menu.dashboard-stats');

        Route::delete('/categories/{id}', [MenuController::class, 'deleteCategory'])->name('menu.categories.delete');

        Route::delete('/subcategories/{id}', [MenuSubcategoryController::class, 'destroy'])->name('subcategories.destroy');

        Route::post('/items', [MenuController::class, 'store'])->name('menu.items.store');

        Route::get('/{id}', [MenuController::class, 'show'])->name('menu.show');

        Route::put('/{id}', [MenuController::class, 'update'])->name('menu.update');

        Route::post('/{id}/restore', [MenuController::class, 'restoreMenu'])->name('menu.restore');
        Route::post('/{id}/archive', [MenuController::class, 'archiveMenu'])->name('menu.archive');

        Route::post('/groups', [MenuController::class, 'storeMenuGroup'])->name('menu.groups.store');

        Route::post('/groups/{id}/display', [MenuController::class, 'updateGroupDisplayStatus']);

        Route::post('/groups/{id}/undisplay', [MenuController::class, 'updateGroupUndisplayStatus'])->name('menu.groups.undisplay');

        Route::post('/groups/{id}/archive', [MenuController::class, 'archiveMenuGroup'])->name('menu.groups.archive');

        Route::post('/groups/{id}/restore', [MenuController::class, 'restoreMenuGroup'])->name('menu.groups.restore');

        Route::delete('/groups/{id}', [MenuController::class, 'deleteMenuGroup'])->name('menu.groups.delete');

        Route::get('/groups/displayed', [MenuController::class, 'getDisplayedMenuGroups'])->name('menu.groups.displayed');

        Route::get('/items/get', [MenuController::class, 'getMenuItems'])->name('menu.items.get');

        Route::get('/categories', [MenuController::class, 'getCategories'])->name('menu.categories');

        Route::get('/print-barcode/{code}/{quantity}', [App\Http\Controllers\MenuController::class, 'printBarcode'])
            ->name('menu.print-barcode');

        Route::delete('/groups/{id}/items', [MenuController::class, 'removeItemsFromGroup'])
            ->name('menu.groups.items.delete');
        Route::post('/groups/{id}/update', [MenuController::class, 'updateMenuGroup'])->name('menu.groups.update');
        Route::get('/available-items/{id}', [MenuController::class, 'getAvailableItems']);
        Route::post('/items-details', [MenuController::class, 'getItemsDetails']);
    });

    Route::get('/menu/code/{code}', [MenuController::class, 'getMenuByCode'])->name('menu.getByCode');

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('categories.index')->middleware('permission:manage_inventory');
        Route::get('/get', [CategoriesController::class, 'list'])->name('categories.get');
        Route::post('/store', [CategoriesController::class, 'store'])->name('categories.store');
        Route::post('/subcategory/store', [CategoriesController::class, 'storeSubcategory'])->name('categories.subcategory.store');
        Route::get('/distribution', [CategoriesController::class, 'getCategoryDistribution'])->name('categories.distribution');
        Route::get('/subcategory/{id}', [CategoriesController::class, 'showSubcategory'])->name('categories.subcategory.show');
        Route::put('/subcategory/{id}', [CategoriesController::class, 'updateSubcategory'])->name('categories.subcategory.update');
        Route::get('/{id}', [CategoriesController::class, 'show'])->name('categories.show');
        Route::post('/delete-subcategories', [CategoriesController::class, 'deleteSubcategories'])->name('categories.subcategories.delete');
        Route::get('/{id}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');
        Route::put('/{id}', [CategoriesController::class, 'update'])->name('categories.update');
        Route::delete('/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
    });

    Route::prefix('pos')->group(function () {
        Route::get('/', fn() => view('pos.index'))->name('pos.index')->middleware('permission:access_pos');
        Route::get('/receipt', [POSController::class, 'generateReceipt'])->name('pos.receipt');
    });

    Route::get(
        '/products/print-barcode/{productCode}/{quantity}',
        [ProductController::class, 'printBarcode']
    )
        ->name('products.print-barcode');

    Route::prefix('sales')->middleware(['auth'])->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('sales.index')->middleware('permission:view_sales');
        Route::get('/get-transactions', [SalesController::class, 'getTransactions'])->name('sales.get-transactions');
        Route::get('/transactions', fn() => view('sales.transactions'))->name('sales.transactions')->middleware('permission:view_sales');
        Route::get('/credit', fn() => view('sales.credit'))->name('sales.credit')->middleware('permission:view_sales');
        Route::get('/transactions/data', [TransactionController::class, 'getTransactions'])->name('transactions.data');
    });

    Route::post('/transactions/store', [TransactionController::class, 'store'])->name('transactions.store');

    Route::post('/products/{product_code}/adjust-inventory', [ProductController::class, 'adjustInventory']);

    Route::get('/transactions/insights', [TransactionController::class, 'getTransactionInsights'])->name('transactions.insights');

    Route::get('/transactions/{id}/details', [TransactionController::class, 'getTransactionDetails'])
        ->name('transactions.details');

    Route::get('/transactions/{id}/print', [TransactionController::class, 'printReceipt'])->name('transactions.print');

    Route::get('/dashboard-summary', [TransactionController::class, 'getDashboardSummary']);
    Route::get('/top-selling-items', [SalesController::class, 'getSellingItems']);

    Route::get('/sales-chart-data', [SalesController::class, 'getSalesChartData']);

    Route::get('/sales-analytics', [SalesController::class, 'getSalesAnalytics'])->name('sales.analytics');
    Route::post('/sales/export-report', [SalesController::class, 'exportSalesReport'])->name('sales.export-report');
    Route::get('/sales/reports/preview', [SalesController::class, 'previewSalesReport'])->name('sales.reports.preview');

    // Enhanced Reports System
    Route::prefix('reports')->middleware(['auth'])->group(function () {
        Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.dashboard');
        Route::get('/inventory', [App\Http\Controllers\ReportController::class, 'inventoryReports'])->name('reports.inventory');
        Route::get('/sales', [App\Http\Controllers\ReportController::class, 'salesReports'])->name('reports.sales');
        
        // Analytics API endpoints
        Route::get('/inventory/analytics', [App\Http\Controllers\ReportController::class, 'getInventoryAnalytics'])->name('reports.inventory.analytics');
        Route::get('/sales/analytics', [App\Http\Controllers\ReportController::class, 'getSalesAnalytics'])->name('reports.sales.analytics');
        
        // Export endpoints
        Route::post('/inventory/export', [App\Http\Controllers\ReportController::class, 'exportInventoryReport'])->name('reports.inventory.export');
        Route::post('/sales/export', [App\Http\Controllers\ReportController::class, 'exportSalesReport'])->name('reports.sales.export');
        
        // Preview endpoints
        Route::get('/inventory/preview', [App\Http\Controllers\ReportController::class, 'previewInventoryReport'])->name('reports.inventory.preview');
        Route::get('/sales/preview', [App\Http\Controllers\ReportController::class, 'previewSalesReport'])->name('reports.sales.preview');
    });

    Route::get('/transactions/credit/summary', [TransactionController::class, 'getCreditSummary'])->name('transactions.credit.summary');
    Route::get('/transactions/credit/list', [TransactionController::class, 'getCreditTransactions'])->name('transactions.credit.list');
    Route::post('/transactions/credit/{id}/approve', [TransactionController::class, 'approveCreditTransaction'])
        ->name('transactions.credit.approve');
    Route::post('/transactions/credit/{id}/decline', [TransactionController::class, 'declineCreditTransaction'])
        ->name('transactions.credit.decline');
    Route::get('/transactions/credit/reports', [TransactionController::class, 'getCreditReports'])
        ->name('transactions.credit.reports');
    Route::get('/transactions/credit/export', [TransactionController::class, 'exportCreditReport'])
        ->name('transactions.credit.export');
    Route::get('/transactions/credit/{id}/details', [TransactionController::class, 'getCreditTransactionDetails'])
        ->name('transactions.credit.details');
    Route::group(['prefix' => 'users', 'middleware' => ['auth']], function () {
        // Static and collection routes FIRST (to avoid being captured by /{id})
        Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware('permission:manage_users');
        Route::get('/data', [UserController::class, 'getData'])->name('users.data');
        Route::get('/permissions', [PermissionController::class, 'index'])->name('users.permissions')->middleware('permission:manage_users');
        Route::get('/roles/list', [UserController::class, 'getRoles'])->name('users.roles');
        Route::get('/all', [UserController::class, 'getAllUsers'])->name('users.all');

        // Create/update actions
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/{id}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');

        // Dynamic ID routes LAST
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    Route::prefix('permissions')->middleware(['auth', 'permission:manage_users'])->group(function () {
        Route::get('/data', [PermissionController::class, 'getData'])->name('permissions.data');
        Route::get('/{userId}', [PermissionController::class, 'getUserPermissions'])->name('permissions.user');
        Route::post('/save', [PermissionController::class, 'savePermissions'])->name('permissions.save');
        Route::get('/role/{roleId}', [PermissionController::class, 'getRolePermissions'])->name('permissions.role');
        Route::post('/role/save', [PermissionController::class, 'saveRolePermissions'])->name('permissions.role.save');
    });

    Route::prefix('roles')->middleware(['auth'])->group(function () {
        Route::post('/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/{id}/update', [RoleController::class, 'update'])->name('roles.update');
        Route::post('/{id}/delete', [RoleController::class, 'destroy'])->name('roles.delete');
    });


    Route::prefix('password-reset')->group(function () {
        Route::middleware(['permission:manage_users'])->group(function () {
            Route::get('/requests', [PasswordResetRequestController::class, 'adminIndex'])
                ->name('admin.password-requests');
            Route::post('/reset/{id}', [PasswordResetRequestController::class, 'resetPassword'])
                ->name('admin.password.reset');
            Route::post('/reject/{id}', [PasswordResetRequestController::class, 'rejectRequest'])
                ->name('admin.password.reject');
        });
    });

    Route::get('/recent-transactions', [TransactionController::class, 'getRecentTransactions']);
    Route::get('/transaction-insights', [TransactionController::class, 'getTransactionInsights']);
    Route::get('/top-selling-items', [SalesController::class, 'getSellingItems']);
    Route::get('/sales-chart-data', [SalesController::class, 'getSalesChartData']);
    Route::get('/sales-analytics', [SalesController::class, 'getSalesAnalytics']);

    // Alias used by dashboard JS: `/inventory-summary`
    Route::get('/inventory-summary', [InventoryController::class, 'getSummaryData'])->name('inventory.summary');

    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    Route::post('/maintenance/toggle', [MaintenanceController::class, 'toggleMaintenance'])->name('toggleMaintenance');

    Route::prefix('scholars')->group(function () {
        Route::get('/', [ScholarController::class, 'index'])->name('scholars.index')->middleware('permission:manage_scholars');
        Route::get('/data', [ScholarController::class, 'getData'])->name('scholars.data')->middleware('permission:manage_scholars');
        Route::post('/', [ScholarController::class, 'store'])->name('scholars.store')->middleware('permission:manage_scholars');
        Route::get('/reports', [ScholarController::class, 'reports'])->name('scholars.reports')->middleware('permission:view_scholar_reports');
        Route::get('/reports/allowances', [ScholarController::class, 'getAllowanceReport'])->name('scholars.reports.allowances')->middleware('permission:view_scholar_reports');
        Route::get('/reports/usage', [ScholarController::class, 'getUsageReport'])->name('scholars.reports.usage')->middleware('permission:view_scholar_reports');
        Route::get('/reports/data', [ScholarController::class, 'getReportsData'])->name('scholars.reports.data')->middleware('permission:view_scholar_reports');
        Route::get('/reports/export', [ScholarController::class, 'exportReport'])->name('scholars.reports.export')->middleware('permission:view_scholar_reports');
        Route::get('/filter-list', [ScholarController::class, 'getScholarsForFilter'])->name('scholars.filter.list')->middleware('permission:view_scholar_reports');
        Route::get('/summary/stats', [ScholarController::class, 'getSummary'])->name('scholars.summary')->middleware('permission:manage_scholars');
        Route::get('/{id}/edit', [ScholarController::class, 'edit'])->name('scholars.edit')->middleware('permission:manage_scholars');
        Route::get('/{id}/history', [ScholarController::class, 'getHistory'])->name('scholars.history')->middleware('permission:manage_scholars');
        Route::get('/{id}', [ScholarController::class, 'show'])->name('scholars.show')->middleware('permission:manage_scholars');
        Route::put('/{id}', [ScholarController::class, 'update'])->name('scholars.update')->middleware('permission:manage_scholars');
        Route::post('/{id}/toggle-status', [ScholarController::class, 'toggleStatus'])->name('scholars.toggle-status')->middleware('permission:manage_scholars');
        Route::post('/reset-daily', [ScholarController::class, 'resetDailyAllowances'])->name('scholars.reset-daily')->middleware('permission:manage_scholars');
    });

    Route::post('/pos/validate-student', [ScholarController::class, 'validateStudent'])->name('pos.validate-student')->middleware('permission:access_pos');

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/general', [SettingsController::class, 'general'])->name('settings.general');
        Route::post('/general/update', [SettingsController::class, 'updateGeneralSettings'])->name('settings.general.update');
        Route::get('/pos', [SettingsController::class, 'pos'])->name('settings.pos');
        Route::post('/pos/update', [SettingsController::class, 'updatePOSSettings'])->name('settings.pos.update');
        Route::get('/roles', [SettingsController::class, 'roles'])->name('settings.roles');
        Route::get('/scholars', [SettingsController::class, 'scholars'])->name('settings.scholars');
        Route::post('/scholars/update', [SettingsController::class, 'updateScholarSettings'])->name('settings.scholars.update');
    });

    Route::prefix('system-logs')->middleware(['permission:view_system_logs'])->group(function () {
        Route::get('/', [SystemLogController::class, 'index'])->name('system-logs.index');
        Route::get('/stats', [SystemLogController::class, 'stats'])->name('system-logs.stats');
        Route::get('/export', [SystemLogController::class, 'export'])->name('system-logs.export');
        Route::get('/{id}', [SystemLogController::class, 'show'])->name('system-logs.show');
    });

    // API Routes for POS integration
    Route::get('/api/settings/tax-rate', function() {
        $settingsService = app(\App\Services\SettingsService::class);
        $settings = $settingsService->getGeneralSettings();
        return response()->json([
            'tax_rate' => $settings['tax_rate'] ?? 0
        ]);
    });

    Route::get('/api/pos-settings/discount-types', function() {
        $settingsService = app(\App\Services\SettingsService::class);
        $settings = $settingsService->getPOSSettings();
        return response()->json([
            'discount_types' => $settings['discount_types'] ?? []
        ]);
    });

    Route::get('/api/pos-settings/departments', function() {
        $settingsService = app(\App\Services\SettingsService::class);
        $settings = $settingsService->getPOSSettings();
        return response()->json([
            'departments' => $settings['departments'] ?? []
        ]);
    });

    Route::get('/api/pos-settings/credit-user-types', function() {
        $settingsService = app(\App\Services\SettingsService::class);
        $settings = $settingsService->getPOSSettings();
        return response()->json([
            'credit_user_types' => $settings['credit_user_types'] ?? []
        ]);
    });
    
    // Debug routes
    Route::get('/debug/scholar-settings', function() {
        $settingsService = app(\App\Services\SettingsService::class);
        $settings = $settingsService->getScholarSettings();
        return response()->json([
            'settings' => $settings,
            'db_settings' => \Illuminate\Support\Facades\DB::table('app_settings')->where('key', 'like', 'scholar.%')->get()
        ]);
    })->name('debug.scholar.settings');
    
    Route::get('/debug/scholar-reports', function() {
        $reports = \App\Models\ScholarBalanceHistory::with(['scholar', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        return response()->json([
            'count' => \App\Models\ScholarBalanceHistory::count(),
            'reports' => $reports
        ]);
    })->name('debug.scholar.reports');
});


