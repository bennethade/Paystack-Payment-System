<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'index'])->name('payment.form');
Route::post('/pay', [PaymentController::class, 'initialize'])->name('pay');
Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
Route::get('/order-details', [PaymentController::class, 'showOrderDetails'])->name('order.details');