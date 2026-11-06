<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomCakeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\UserController;

// Public Auth & Role Switcher
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/switch-role', [AuthController::class, 'switchRole']);
Route::get('/auth/users', [AuthController::class, 'listUsers']);

// Public Branches & Catalog
Route::get('/branches', [BranchController::class, 'index']);
Route::get('/branches/{id}', [BranchController::class, 'show']);
Route::get('/branches/{id}/delivery-zones', [BranchController::class, 'deliveryZones']);
Route::get('/categories', [CatalogController::class, 'categories']);
Route::get('/products', [CatalogController::class, 'products']);
Route::get('/products/{id}', [CatalogController::class, 'show']);
Route::get('/catalog/products', [CatalogController::class, 'products']);
Route::get('/catalog/products/{id}', [CatalogController::class, 'show']);
Route::get('/catalog/products/{id}/branch-availability', [CatalogController::class, 'branchAvailability']);
Route::get('/products/{id}/branch-availability', [CatalogController::class, 'branchAvailability']);
Route::get('/branches/{id}/products', [CatalogController::class, 'products']);

// Public Payment Webhook
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

// Core application endpoints
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// Admin & Branch Scoped Endpoints - Protected with RoleAuthorizationMiddleware
Route::prefix('/admin')->middleware(['admin.auth'])->group(function () {
    // Overview (Super Admin, Main Branch Manager, Branch Admin)
    Route::get('/overview', [OverviewController::class, 'index']);

    // Users and Roles (Super Admin full, Main Branch Manager limited to Main Branch)
    Route::get('/users-list', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::patch('/users/{id}', [UserController::class, 'update']);

    // Inventory (Scoped by controller to caller's branch if Branch Admin / Staff)
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust']);
    Route::get('/inventory/transactions', [InventoryController::class, 'transactions']);
    Route::get('/inventory/reconcile', [InventoryController::class, 'reconcile']);

    // Transfers & Distribution
    Route::get('/transfers/dashboard', [TransferController::class, 'dashboard']);
    Route::get('/transfers', [TransferController::class, 'index']);
    Route::post('/transfers', [TransferController::class, 'store'])->middleware('role:Super Admin,Main Branch Manager');
    Route::post('/transfers/{id}/approve', [TransferController::class, 'approve'])->middleware('role:Super Admin');
    Route::post('/transfers/{id}/dispatch', [TransferController::class, 'dispatch'])->middleware('role:Super Admin,Main Branch Manager');
    Route::post('/transfers/{id}/receive', [TransferController::class, 'receive']);
    Route::post('/transfers/{id}/resolve', [TransferController::class, 'resolve'])->middleware('role:Super Admin,Main Branch Manager');
    Route::post('/stock-requests/{id}/convert', [TransferController::class, 'convertRequest'])->middleware('role:Super Admin,Main Branch Manager');

    // Orders (Scoped by controller to caller's branch if Branch Admin / Staff)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Custom Cakes (Scoped by controller to caller's branch if Branch Admin)
    Route::get('/custom-cakes', [CustomCakeController::class, 'index']);
    Route::get('/custom-cakes/{id}', [CustomCakeController::class, 'show']);
    Route::patch('/custom-cakes/{id}/quote', [CustomCakeController::class, 'quote']);
    Route::patch('/custom-cakes/{id}/status', [CustomCakeController::class, 'updateStatus']);

    // Payments & Refunds
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments/refund', [PaymentController::class, 'refund'])->middleware('role:Super Admin,Main Branch Manager');

    // Reports (Scoped by controller)
    Route::get('/reports/summary', [ReportController::class, 'summary']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/alert-rules', [NotificationController::class, 'saveAlertRules'])->middleware('role:Super Admin');

    // Settings (Super Admin Only)
    Route::get('/settings', [SettingController::class, 'index'])->middleware('role:Super Admin');
    Route::post('/settings', [SettingController::class, 'update'])->middleware('role:Super Admin');

    // Audit Log (Super Admin Only)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('role:Super Admin');

    // Branch management (Super Admin Only)
    Route::post('/branches', [BranchController::class, 'store'])->middleware('role:Super Admin');
    Route::patch('/branches/{id}', [BranchController::class, 'update'])->middleware('role:Super Admin');
    Route::post('/branches/{id}/delivery-zones', [BranchController::class, 'addDeliveryZone'])->middleware('role:Super Admin');
    Route::delete('/branches/{id}/delivery-zones/{zoneId}', [BranchController::class, 'deleteDeliveryZone'])->middleware('role:Super Admin');

    // Catalog & Pricing Management (Super Admin Only)
    Route::post('/products', [CatalogController::class, 'store'])->middleware('role:Super Admin');
    Route::patch('/products/{id}', [CatalogController::class, 'update'])->middleware('role:Super Admin');
    Route::post('/categories', [CatalogController::class, 'storeCategory'])->middleware('role:Super Admin');
    Route::post('/products/{id}/branch-price', [CatalogController::class, 'setBranchPrice'])->middleware('role:Super Admin');
});

// Customer Storefront Order & Cake Endpoints
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/release-expired', [OrderController::class, 'releaseExpiredEndpoint']);
Route::get('/custom-cakes', [CustomCakeController::class, 'index']);
Route::post('/custom-cakes', [CustomCakeController::class, 'store']);
Route::get('/custom-cakes/{id}', [CustomCakeController::class, 'show']);
Route::post('/custom-cakes/{id}/accept-quote', [CustomCakeController::class, 'acceptQuote']);
Route::post('/custom-cakes/{id}/cancel', [CustomCakeController::class, 'cancel']);
