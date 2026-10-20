<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\AssetHistoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LoanController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');

    Route::resource('categories', CategoryController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('locations', LocationController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('vendors', VendorController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::resource('assets', AssetController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('maintenance-records', MaintenanceRecordController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/create', [TransferController::class, 'create'])->name('transfers.create');
    Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
    Route::get('/transfers/{transfer}', [TransferController::class, 'show'])->name('transfers.show');
});

Route::middleware(['auth', 'role:admin,staff,peminjam'])->group(function () {
    Route::get('/borrowings', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/borrowings/history', [LoanController::class, 'history'])->name('loans.history');
});

Route::middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/borrowings/create/{asset}', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/borrowings', [LoanController::class, 'store'])->name('loans.store');
    Route::put('/borrowings/{loan}/return', [LoanController::class, 'returnAsset'])->name('loans.return');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);
    Route::resource('locations', LocationController::class)->only(['index', 'show']);
    Route::resource('vendors', VendorController::class)->only(['index', 'show']);
});

Route::middleware(['auth', 'role:admin,staff,pimpinan,peminjam'])->group(function () {
    Route::resource('assets', AssetController::class)->only(['index', 'show']);
});

Route::middleware(['auth', 'role:admin,staff,pimpinan'])->group(function () {
    Route::resource('maintenance-records', MaintenanceRecordController::class)->only(['index', 'show']);
    Route::resource('asset-histories', AssetHistoryController::class)->only(['index', 'show']);
});

Route::middleware(['auth', 'role:admin,pimpinan'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/approvals', [TransferController::class, 'approvalIndex'])->name('approvals.index');
    Route::get('/approvals/{transfer}', [TransferController::class, 'approvalShow'])->name('approvals.show');
    Route::put('/approvals/{transfer}/approve', [TransferController::class, 'approve'])->name('approvals.approve');
    Route::put('/approvals/{transfer}/reject', [TransferController::class, 'reject'])->name('approvals.reject');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
});

Route::middleware(['auth', 'role:admin,staff,pimpinan'])->group(function () {
    Route::get('/ai', [AiController::class, 'index'])->name('ai.index');
    Route::post('/ai/chat', [AiController::class, 'chat'])->name('ai.chat');
});