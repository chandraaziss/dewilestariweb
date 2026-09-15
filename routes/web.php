<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierStockController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\CourierAuthController;
use App\Http\Controllers\CourierController;

Route::get('/', [HomeController::class, 'index'])->middleware('auth');
Route::get('/track', function() {
    return view('tracking');
});

Route::get('/api/products', [ProductController::class, 'api']);
Route::middleware('admin')->group(function() {
// Route::get('/admin/products', [ProductController::class, 'index']);
Route::get('/admin/orders',[OrderController::class,'index']);
Route::get('/admin/reports',[OrderController::class, 'report']);
Route::get('/admin/reports/store', [OrderController::class, 'storeReport']);
Route::get('/admin/reports/order-detail/{id}', [OrderController::class, 'reportOrderDetail'])->name('reports.order_detail');
Route::get('/admin/reports/store/pdf', [OrderController::class, 'downloadStoreReportPdf']);
Route::get('/admin/reports/suppliers', function() { return redirect('/admin/reports/store'); });
Route::get('/admin/reports/suppliers/pdf', function() { return redirect('/admin/reports/store'); });
Route::get('/admin/reports/export', [OrderController::class, 'exportReport']);
Route::get('/admin/suppliers', [SupplierController::class, 'index']);
Route::get('/admin/suppliers/manage', [SupplierController::class, 'manage']);
Route::post('/admin/suppliers/manage', [SupplierController::class, 'store']);
Route::get('/admin/suppliers/{slug}/edit', [SupplierController::class, 'edit']);
Route::put('/admin/suppliers/{slug}', [SupplierController::class, 'update']);
Route::delete('/admin/suppliers/{slug}', [SupplierController::class, 'destroy']);
Route::get('/admin/suppliers/{slug}', [SupplierController::class, 'show']);
Route::post('/admin/suppliers/{slug}/order', [SupplierController::class, 'sendOrder']);
Route::get('/admin/supplier-orders/{id}/invoice', [SupplierController::class, 'showInvoice']);
Route::post('/admin/supplier-orders/{id}/complete', [SupplierController::class, 'completeOrder']);

// Retur Produk Supplier Routes
Route::post('/admin/supplier-returns/store', [SupplierController::class, 'storeReturn']);
Route::get('/admin/supplier-returns/{id}/ticket', [SupplierController::class, 'showReturnTicket']);
Route::post('/admin/supplier-returns/{id}/status', [SupplierController::class, 'updateReturnStatus']);
Route::delete('/admin/supplier-returns/{id}', [SupplierController::class, 'destroyReturn']);

Route::post('/admin/orders/{id}/tracking', [OrderController::class, 'updateTrackingStatus']);
Route::post('/admin/orders/{id}/notify-discrepancy', [OrderController::class, 'notifyPaymentDiscrepancy']);
Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy']);
Route::get('/admin/pay/{id}', [OrderController::class, 'simulatePayment']);
Route::get('/admin/chat', [ChatController::class, 'adminIndex']);
Route::get('/admin/chat/unread', [ChatController::class, 'unreadCount']);
Route::get('/admin/chat/{session_id}', [ChatController::class, 'adminShow']);
Route::get('/admin/chat/{session_id}/fetch', [ChatController::class, 'adminFetch']);
Route::post('/admin/chat/{session_id}', [ChatController::class, 'adminReply']);

Route::get('/admin/customers', [CustomerController::class, 'adminIndex']);
Route::get('/admin/customers/{id}/edit', [CustomerController::class, 'adminEdit']);
Route::put('/admin/customers/{id}', [CustomerController::class, 'adminUpdate']);
Route::delete('/admin/customers/{id}', [CustomerController::class, 'adminDestroy']);

Route::get('/admin/ratings', [RatingController::class, 'adminIndex']);
        });
Route::get('/admin/login', [AdminAuthController::class, 'loginForm']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::get('/admin/logout', [AdminAuthController::class, 'logout']);


Route::get('/admin/supplier-stocks', [SupplierStockController::class, 'index']);
Route::get('/admin/supplier-stocks/create', [SupplierStockController::class, 'create']);
Route::post('/admin/supplier-stocks/store', [SupplierStockController::class, 'store']);
Route::post('/admin/supplier-stocks/store-batch-queue', [SupplierStockController::class, 'storeBatchQueue']);
Route::get('/admin/supplier-stocks/expired', [SupplierStockController::class, 'expiredForm']);
Route::post('/admin/supplier-stocks/expired', [SupplierStockController::class, 'storeExpired']);
Route::get('/admin/supplier-stocks/expired-list', [SupplierStockController::class, 'expiredList']);
Route::get('/admin/supplier-stocks/expired/{id}/edit', [SupplierStockController::class, 'editExpired']);
Route::put('/admin/supplier-stocks/expired/{id}', [SupplierStockController::class, 'updateExpired']);
Route::delete('/admin/supplier-stocks/expired/{id}', [SupplierStockController::class, 'destroyExpired']);
Route::post('/admin/supplier-stocks/{id}/launch', [SupplierStockController::class, 'launchBatch']);
Route::get('/admin/supplier-stocks/{id}/edit', [SupplierStockController::class, 'edit']);
Route::put('/admin/supplier-stocks/{id}', [SupplierStockController::class, 'update']);
Route::delete('/admin/supplier-stocks/{id}', [SupplierStockController::class, 'destroy']);

// Route::get('/admin/products/create', [ProductController::class, 'create']);
// Route::post('/admin/products/store', [ProductController::class, 'store']);
// 
// Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit']);
// Route::put('/admin/products/{id}', [ProductController::class, 'update']);
// 
Route::get('/admin/orders/{id}', [OrderController::class, 'show']);
// Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

Route::post('/checkout', [OrderController::class, 'checkout']);
Route::post('/midtrans/callback', [OrderController::class, 'callback']);
Route::post('/upload-transfer-proof/{id}', [OrderController::class, 'uploadTransferProof']);
Route::post('/admin/orders/{id}/verify-payment', [OrderController::class, 'verifyBankPayment']);

Route::get('/check-payment/{orderId}',[OrderController::class, 'checkPayment']);
Route::get('/check-pending-order', [OrderController::class, 'checkPendingOrder']);
Route::post('/cancel-pending-order/{orderId}', [OrderController::class, 'cancelPendingOrder']);
Route::get('/track-order/{ticketId}', [OrderController::class, 'trackOrder']);
Route::post('/track-order/{ticketId}/return', [OrderController::class, 'requestReturn']);
Route::post('/track-order/{ticketId}/reupload-proof', [OrderController::class, 'reuploadTransferProof']);

Route::get('/test-midtrans/{id}', function($id) {
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = false;
    $statusResp = \Midtrans\Transaction::status($id);
    return response()->json($statusResp);
});

Route::post('/api/chat/send', [ChatController::class, 'sendMessage']);
Route::get('/api/chat/fetch', [ChatController::class, 'fetchMessages']);

// Customer Authentication & Dashboard Routes
Route::get('/register', [CustomerController::class, 'showRegisterForm']);
Route::post('/register', [CustomerController::class, 'register']);
Route::get('/login', [CustomerController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomerController::class, 'login']);
Route::get('/logout', [CustomerController::class, 'logout']);

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function() {
    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard']);
    Route::post('/customer/profile', [CustomerController::class, 'updateProfile']);
    Route::post('/customer/addresses', [CustomerController::class, 'storeAddress']);
    Route::post('/customer/addresses/{id}/primary', [CustomerController::class, 'setPrimaryAddress']);
    Route::delete('/customer/addresses/{id}', [CustomerController::class, 'deleteAddress']);
    Route::get('/customer/orders/{id}/rate', [RatingController::class, 'create']);
    Route::post('/customer/orders/{id}/rate', [RatingController::class, 'store']);
});

Route::prefix('courier')->middleware('admin')->group(function () {
    Route::get('/dashboard', [CourierController::class, 'dashboard'])->name('courier.dashboard');
    Route::get('/deliveries', [CourierController::class, 'index'])->name('courier.deliveries.index');
    Route::get('/deliveries/{id}', [CourierController::class, 'show'])->name('courier.deliveries.show');
    Route::post('/deliveries/{id}/status', [CourierController::class, 'updateStatus'])->name('courier.deliveries.status');
    Route::get('/history', [CourierController::class, 'history'])->name('courier.history');
});
