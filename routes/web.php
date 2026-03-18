<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SizeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Appointment routes
 */
Route::group(['prefix' => 'appointment', 'as' => 'appointment.'], function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index');

    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::get('/config', [AppointmentController::class, 'config'])->name('config');
        Route::post('/get', [AppointmentController::class, 'get'])->name('get');
        Route::post('/put', [AppointmentController::class, 'put'])->name('put');
    });
});

/**
 * Size routes
 */
Route::group(['prefix' => 'size', 'as' => 'size.'], function () {
    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::post('/get', [SizeController::class, 'get'])->name('get');
    });
});

/**
 * Service routes
 */
Route::group(['prefix' => 'service', 'as' => 'service.'], function () {
    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::post('/get', [ServiceController::class, 'get'])->name('get');
    });
});

Auth::routes();

Route::group(['prefix' => 'panel', 'as' => 'panel.'], function () {
    // Page routes (just return views — Vue components handle all data)
    Route::get('/', [App\Http\Controllers\PanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [App\Http\Controllers\PanelController::class, 'appointments'])->name('appointments');
    Route::get('/services', [App\Http\Controllers\PanelController::class, 'services'])->name('services');
    Route::get('/sales', [App\Http\Controllers\PanelController::class, 'salesReport'])->name('sales');
    Route::get('/inventory', [App\Http\Controllers\PanelController::class, 'inventory'])->name('inventory');
    Route::get('/notifications', [App\Http\Controllers\PanelController::class, 'notifications'])->name('notifications');
    Route::get('/settings', [App\Http\Controllers\PanelController::class, 'settings'])->name('settings');

    // Admin-only page routes
    Route::middleware('admin')->group(function () {
        Route::get('/users', [App\Http\Controllers\PanelController::class, 'users'])->name('users');
    });

    // JSON API endpoints (consumed by Vue components)
    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::get('/dashboard', [App\Http\Controllers\PanelApiController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [App\Http\Controllers\PanelApiController::class, 'appointments'])->name('appointments');
        Route::post('/appointments', [App\Http\Controllers\PanelApiController::class, 'storeAppointment'])->name('appointments.store');
        Route::put('/appointments/{appointment}', [App\Http\Controllers\PanelApiController::class, 'updateAppointment'])->name('appointments.update');
        Route::patch('/appointments/{appointment}/status', [App\Http\Controllers\PanelApiController::class, 'updateAppointmentStatus'])->name('appointments.status');
        Route::delete('/appointments/{appointment}', [App\Http\Controllers\PanelApiController::class, 'destroyAppointment'])->name('appointments.destroy');
        Route::get('/services', [App\Http\Controllers\PanelApiController::class, 'services'])->name('services');
        Route::post('/services', [App\Http\Controllers\PanelApiController::class, 'storeService'])->name('services.store');
        Route::put('/services/{service}', [App\Http\Controllers\PanelApiController::class, 'updateService'])->name('services.update');
        Route::delete('/services/{service}', [App\Http\Controllers\PanelApiController::class, 'destroyService'])->name('services.destroy');
        Route::post('/sizes', [App\Http\Controllers\PanelApiController::class, 'storeSize'])->name('sizes.store');
        Route::put('/sizes/{size}', [App\Http\Controllers\PanelApiController::class, 'updateSize'])->name('sizes.update');
        Route::delete('/sizes/{size}', [App\Http\Controllers\PanelApiController::class, 'destroySize'])->name('sizes.destroy');
        Route::get('/sales', [App\Http\Controllers\PanelApiController::class, 'salesReport'])->name('sales');
        Route::get('/export-report', [App\Http\Controllers\PanelApiController::class, 'exportDailyReport'])->name('export.report');

        // Inventory
        Route::get('/inventory', [App\Http\Controllers\PanelApiController::class, 'inventory'])->name('inventory');
        Route::post('/inventory/categories', [App\Http\Controllers\PanelApiController::class, 'storeInventoryCategory'])->name('inventory.categories.store');
        Route::put('/inventory/categories/{inventoryCategory}', [App\Http\Controllers\PanelApiController::class, 'updateInventoryCategory'])->name('inventory.categories.update');
        Route::delete('/inventory/categories/{inventoryCategory}', [App\Http\Controllers\PanelApiController::class, 'destroyInventoryCategory'])->name('inventory.categories.destroy');
        Route::post('/inventory/items', [App\Http\Controllers\PanelApiController::class, 'storeInventoryItem'])->name('inventory.items.store');
        Route::put('/inventory/items/{inventoryItem}', [App\Http\Controllers\PanelApiController::class, 'updateInventoryItem'])->name('inventory.items.update');
        Route::delete('/inventory/items/{inventoryItem}', [App\Http\Controllers\PanelApiController::class, 'destroyInventoryItem'])->name('inventory.items.destroy');
        Route::post('/inventory/items/{inventoryItem}/stock', [App\Http\Controllers\PanelApiController::class, 'adjustStock'])->name('inventory.items.stock');
        Route::get('/inventory/logs', [App\Http\Controllers\PanelApiController::class, 'inventoryLogs'])->name('inventory.logs');

        // Notifications
        Route::get('/notifications', [App\Http\Controllers\PanelApiController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/unread-count', [App\Http\Controllers\PanelApiController::class, 'notificationsUnreadCount'])->name('notifications.unread');
        Route::patch('/notifications/{notification}/read', [App\Http\Controllers\PanelApiController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\PanelApiController::class, 'markAllNotificationsRead'])->name('notifications.readAll');
        Route::delete('/notifications/{notification}', [App\Http\Controllers\PanelApiController::class, 'destroyNotification'])->name('notifications.destroy');
        Route::post('/notifications/clear-read', [App\Http\Controllers\PanelApiController::class, 'clearReadNotifications'])->name('notifications.clearRead');

        // Settings
        Route::get('/settings', [App\Http\Controllers\PanelApiController::class, 'settings'])->name('settings');
        Route::put('/settings/profile', [App\Http\Controllers\PanelApiController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [App\Http\Controllers\PanelApiController::class, 'updatePassword'])->name('settings.password');

        // Admin-only routes
        Route::middleware('admin')->group(function () {
            // Business settings (admin only)
            Route::put('/settings/business', [App\Http\Controllers\PanelApiController::class, 'updateBusinessSettings'])->name('settings.business');
            Route::delete('/settings/business/logo', [App\Http\Controllers\PanelApiController::class, 'removeLogo'])->name('settings.business.logo.remove');

            // Users management (admin only)
            Route::get('/users', [App\Http\Controllers\PanelApiController::class, 'users'])->name('users');
            Route::post('/users', [App\Http\Controllers\PanelApiController::class, 'storeUser'])->name('users.store');
            Route::put('/users/{user}', [App\Http\Controllers\PanelApiController::class, 'updateUser'])->name('users.update');
            Route::delete('/users/{user}', [App\Http\Controllers\PanelApiController::class, 'destroyUser'])->name('users.destroy');
        });
    });
});
