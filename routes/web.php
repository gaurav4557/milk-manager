<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MilkEntryController;
use App\Http\Controllers\MilkRateController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerHistoryController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Management
    Route::resource('customers', CustomerController::class)->except(['show']);

    // Customer Profile and History
    Route::get('/customers/{id}/history', [CustomerHistoryController::class, 'show'])
        ->whereNumber('id')
        ->name('customers.history');

    // Daily Milk Entries
    Route::get('/milk-entries', [MilkEntryController::class, 'index'])->name('milk-entries.index');
    Route::post('/milk-entries', [MilkEntryController::class, 'store'])->name('milk-entries.store');
    Route::delete('/milk-entries/{id}', [MilkEntryController::class, 'destroy'])
        ->whereNumber('id')
        ->name('milk-entries.destroy');

    // Milk Rates
    Route::get('/milk-rates', [MilkRateController::class, 'index'])->name('milk-rates.index');
    Route::post('/milk-rates', [MilkRateController::class, 'store'])->name('milk-rates.store');

    // Monthly Bills
    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::post('/bills/generate', [BillController::class, 'generate'])->name('bills.generate');
    Route::get('/bills/{id}', [BillController::class, 'show'])->name('bills.show');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    // Revenue Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
