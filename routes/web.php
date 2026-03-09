<?php

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
