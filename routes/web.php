<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerTransactionController;
use App\Http\Controllers\InventoryAuditController;
use App\Http\Controllers\InventoryRequestController;
use App\Http\Controllers\BranchManagerReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffInventoryController;
use App\Http\Controllers\StaffInventoryRequestController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Old Breeze "dashboard" URL: send each person to their own dashboard
Route::get('/dashboard', function () {
    if (Auth::guard('staff')->check()) {
        return redirect()->route('staff.dashboard');
    }

    if (Auth::guard('web')->check()) {
        return redirect()->route('branch-manager.dashboard');
    }

    return redirect()->route('login');
})->name('dashboard');

Route::middleware('auth:web')
    ->prefix('branch-manager')
    ->name('branch-manager.')
    ->group(function () {

        // Branch Manager Dashboard
        Route::get('/dashboard', [DashboardController::class, 'manager'])->name('dashboard');

        // Branch Manager Reports (read-only summaries of this system's recorded data)
        Route::get('reports', [BranchManagerReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [BranchManagerReportController::class, 'export'])->name('reports.export');

        // Staff Management
        Route::resource('users', StaffController::class)->parameters(['users' => 'staff']);

        // Inventory (manager only observes; changes come from approved staff requests)
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');

        // Inventory audits (manager reviews drafts and finalized reports)
        Route::get('audits', [InventoryAuditController::class, 'managerIndex'])->name('audits.index');
        Route::get('audits/{audit}', [InventoryAuditController::class, 'managerShow'])->name('audits.show');

        // Customer service transactions (manager review only)
        Route::get('transactions', [CustomerTransactionController::class, 'managerIndex'])->name('transactions.index');
        Route::get('transactions/{transaction}', [CustomerTransactionController::class, 'managerShow'])->name('transactions.show');

        // Staff inventory requests (manager reviews)
        Route::get('inventory-requests', [InventoryRequestController::class, 'index'])->name('inventory-requests.index');
        Route::post('inventory-requests/{inventoryRequest}/approve', [InventoryRequestController::class, 'approve'])->name('inventory-requests.approve');
        Route::post('inventory-requests/{inventoryRequest}/decline', [InventoryRequestController::class, 'decline'])->name('inventory-requests.decline');
    });

/*
|--------------------------------------------------------------------------
| Staff area (own guard, own layout)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:staff')
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');
        Route::get('/transactions', [CustomerTransactionController::class, 'staffIndex'])->name('transactions');
        Route::get('/transactions/create', [CustomerTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [CustomerTransactionController::class, 'store'])->name('transactions.store');
        Route::post('/transactions/{transaction}/payments', [CustomerTransactionController::class, 'recordPayment'])->name('transactions.payments.store');
        Route::get('/transactions/{transaction}', [CustomerTransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/edit', [CustomerTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [CustomerTransactionController::class, 'update'])->name('transactions.update');
        Route::post('/transactions/{transaction}/complete', [CustomerTransactionController::class, 'complete'])->name('transactions.complete');
        Route::post('/transactions/{transaction}/void', [CustomerTransactionController::class, 'void'])->name('transactions.void');

        // Inventory: view + submit requests (manager approves)
        Route::get('/inventory', [StaffInventoryController::class, 'index'])->name('inventory');
        Route::get('/inventory/requests', [StaffInventoryRequestController::class, 'index'])->name('inventory.requests');
        Route::get('/inventory/requests/create', [StaffInventoryRequestController::class, 'create'])->name('inventory.requests.create');
        Route::post('/inventory/requests', [StaffInventoryRequestController::class, 'store'])->name('inventory.requests.store');

        Route::get('/audits', [InventoryAuditController::class, 'staffIndex'])->name('audits');
        Route::get('/audits/create', [InventoryAuditController::class, 'create'])->name('audits.create');
        Route::post('/audits', [InventoryAuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{audit}', [InventoryAuditController::class, 'staffShow'])->name('audits.show');
        Route::put('/audits/{audit}', [InventoryAuditController::class, 'update'])->name('audits.update');
        Route::post('/audits/{audit}/finalize', [InventoryAuditController::class, 'finalize'])->name('audits.finalize');
    });

Route::middleware('auth:web')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';