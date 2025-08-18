<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {

    // Authentication routes
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Routes protected with Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/lab/orders', [LabOrderController::class, 'index'])->name('lab.orders.index');
        Route::get('/lab/orders/{labOrder}', [LabOrderController::class, 'show'])->name('lab.orders.show');
        Route::post('/lab/orders', [LabOrderController::class, 'store'])->name('lab.orders.store');
        Route::patch('/lab/orders/{labOrder}/status', [LabOrderController::class, 'updateStatus'])->name('lab.orders.updateStatus');
        Route::delete('/lab/orders/{labOrder}', [LabOrderController::class, 'destroy'])->name('lab.orders.destroy');
    });
    
});
