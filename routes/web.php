<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\LeadManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('storefront.home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{slug}/configure', [ProductController::class, 'configure'])->name('products.configure');
Route::get('/quote-request', [QuoteRequestController::class, 'create'])->name('quote-requests.create');
Route::post('/quote-request', [QuoteRequestController::class, 'store'])->name('quote-requests.store');
Route::get('/quote-request/success', [QuoteRequestController::class, 'success'])->name('quote-requests.success');
Route::get('/portal', [CustomerPortalController::class, 'index'])->name('portal.index');
Route::post('/portal/lookup', [CustomerPortalController::class, 'lookup'])->name('portal.lookup');
Route::get('/portal/requests/{trackingCode}', [CustomerPortalController::class, 'show'])->name('portal.requests.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/leads', [LeadManagementController::class, 'index'])->name('leads.index');
        Route::get('/leads/{lead}', [LeadManagementController::class, 'show'])->name('leads.show');
        Route::patch('/leads/{lead}/status', [LeadManagementController::class, 'updateStatus'])->name('leads.update-status');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
    });
});
