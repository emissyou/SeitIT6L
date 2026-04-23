<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\WetstockController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/shift-management', [ShiftController::class, 'index'])->name('shift.management');
Route::post('/shift/open', [ShiftController::class, 'open'])->name('shift.open');
Route::post('/shift/close', [ShiftController::class, 'close'])->name('shift.close');
Route::get('/shift/{shift}/view', [ShiftController::class, 'view'])->name('shift.view');
Route::get('/shift/{shift}/edit', [ShiftController::class, 'edit'])->name('shift.edit');
Route::patch('/shift/{shift}/update', [ShiftController::class, 'update'])->name('shift.update');
Route::patch('/shift/{shift}/archive', [ShiftController::class, 'archive'])->name('shift.archive');
Route::patch('/shift/{shift}/restore', [ShiftController::class, 'restore'])->name('shift.restore');
Route::delete('/shift/{shift}', [ShiftController::class, 'destroy'])->name('shift.destroy');
Route::get('/wetstock', [WetstockController::class, 'index'])->name('wetstock');
Route::post('/wetstock/delivery', [WetstockController::class, 'store'])->name('wetstock.delivery.store');
Route::get('/reports', [ReportController::class, 'index'])->name('reports');
Route::get('/financials', [FinancialController::class, 'index'])->name('financials');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
Route::get('/customer/{customer}/view', [CustomerController::class, 'view'])->name('customer.view');
Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
Route::patch('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
Route::patch('/customer/{customer}/archive', [CustomerController::class, 'archive'])->name('customer.archive');
Route::patch('/customer/{customer}/restore', [CustomerController::class, 'restore'])->name('customer.restore');
Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
