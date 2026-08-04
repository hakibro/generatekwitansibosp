<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReceiptBatchController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/imports/create', [ImportController::class, 'create'])->name('imports.create');
    Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');

    Route::get('/batches/{batch}/edit', [ReceiptBatchController::class, 'edit'])->name('batches.edit');
    Route::put('/batches/{batch}', [ReceiptBatchController::class, 'update'])->name('batches.update');
    Route::get('/batches/{batch}/print', [ReceiptBatchController::class, 'print'])->name('batches.print');
    Route::get('/batches/{batch}/print-v2', [ReceiptBatchController::class, 'printV2'])->name('batches.print-v2');
    Route::delete('/batches/{batch}', [ReceiptBatchController::class, 'destroy'])->name('batches.destroy');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/settings/password', [SettingsController::class, 'changePassword'])->name('settings.password');
});
