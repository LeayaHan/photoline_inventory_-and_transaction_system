<?php

use App\Http\Controllers\BranchManagerReportController;
use App\Http\Controllers\CustomerTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryAuditController;
use App\Http\Controllers\InventoryBatchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceMaterialRuleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffInventoryController;
use App\Http\Controllers\StaffInventoryRequestController;
use App\Http\Middleware\EnsureBranchManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Shared Breeze dashboard entry point
|--------------------------------------------------------------------------
| Staff and the Branch Manager use separate guards, dashboards, and layouts.
| This route only sends an authenticated account to its own dashboard.
*/
Route::get('/dashboard', function () {
    if (Auth::guard('staff')->check()) {
        return redirect()->route('staff.dashboard');
    }

    $manager = Auth::guard('web')->user();

    if ($manager && strtolower((string) $manager->email) === 'photolinemanager@gmail.com') {
        return redirect()->route('branch-manager.dashboard');
    }

    if ($manager) {
        Auth::guard('web')->logout();
    }

    return redirect()->route('login');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Branch Manager area
|--------------------------------------------------------------------------
| Every route here requires the web guard AND the designated manager account.
| Route names and existing controller actions are preserved.
*/
Route::middleware(['auth:web', EnsureBranchManager::class])
    ->prefix('branch-manager')
    ->name('branch-manager.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'manager'])->name('dashboard');

        // Reports
        Route::get('/reports', [BranchManagerReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [BranchManagerReportController::class, 'export'])->name('reports.export');

        // Staff account management
        Route::resource('users', StaffController::class)->parameters(['users' => 'staff']);

        // Inventory overview (stock changes are handled through approved requests)
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

        // Raw-material usage rules for services
        Route::get('/service-material-rules', [ServiceMaterialRuleController::class, 'index'])
            ->name('service-material-rules.index');
        Route::post('/service-material-rules', [ServiceMaterialRuleController::class, 'store'])
            ->name('service-material-rules.store');
        Route::delete('/service-material-rules/{serviceMaterialRule}', [ServiceMaterialRuleController::class, 'destroy'])
            ->name('service-material-rules.destroy');

        // Inventory audits
        Route::get('/audits', [InventoryAuditController::class, 'managerIndex'])->name('audits.index');
        Route::get('/audits/{audit}', [InventoryAuditController::class, 'managerShow'])->name('audits.show');

        // Customer transactions (manager review)
        Route::get('/transactions', [CustomerTransactionController::class, 'managerIndex'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [CustomerTransactionController::class, 'managerShow'])->name('transactions.show');

        // Staff inventory requests
        Route::get('/inventory-requests', [InventoryRequestController::class, 'index'])->name('inventory-requests.index');
        Route::post('/inventory-requests/{inventoryRequest}/approve', [InventoryRequestController::class, 'approve'])
            ->name('inventory-requests.approve');
        Route::post('/inventory-requests/{inventoryRequest}/decline', [InventoryRequestController::class, 'decline'])
            ->name('inventory-requests.decline');
    });

/*
|--------------------------------------------------------------------------
| Staff area
|--------------------------------------------------------------------------
| Staff use their own guard and staff layout. No manager reports or manager
| routes are included in this group.
*/
Route::middleware('auth:staff')
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');

        // Customer transactions
        Route::get('/transactions', [CustomerTransactionController::class, 'staffIndex'])->name('transactions');
        Route::get('/transactions/create', [CustomerTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [CustomerTransactionController::class, 'store'])->name('transactions.store');
        Route::post('/transactions/{transaction}/payments', [CustomerTransactionController::class, 'recordPayment'])
            ->name('transactions.payments.store');
        Route::get('/transactions/{transaction}', [CustomerTransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/edit', [CustomerTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [CustomerTransactionController::class, 'update'])->name('transactions.update');
        Route::post('/transactions/{transaction}/complete', [CustomerTransactionController::class, 'complete'])
            ->name('transactions.complete');
        Route::post('/transactions/{transaction}/void', [CustomerTransactionController::class, 'void'])
            ->name('transactions.void');

        // Inventory viewing and staff requests
        Route::get('/inventory', [StaffInventoryController::class, 'index'])->name('inventory');
        Route::get('/inventory/requests', [StaffInventoryRequestController::class, 'index'])->name('inventory.requests');
        Route::get('/inventory/requests/create', [StaffInventoryRequestController::class, 'create'])->name('inventory.requests.create');
        Route::post('/inventory/requests', [StaffInventoryRequestController::class, 'store'])->name('inventory.requests.store');

        // Inventory batches and service-material rules used by the staff inventory screen
        Route::get('/inventory-batches', [InventoryBatchController::class, 'index'])->name('inventory-batches.index');
        Route::post('/inventory-batches', [InventoryBatchController::class, 'storeBatch'])->name('inventory-batches.store');
        Route::post('/inventory-batches/rules', [InventoryBatchController::class, 'storeRule'])
            ->name('inventory-batches.rules.store');
        Route::delete('/inventory-batches/rules/{rule}', [InventoryBatchController::class, 'destroyRule'])
            ->name('inventory-batches.rules.destroy');

        // Inventory audits
        Route::get('/audits', [InventoryAuditController::class, 'staffIndex'])->name('audits');
        Route::get('/audits/create', [InventoryAuditController::class, 'create'])->name('audits.create');
        Route::post('/audits', [InventoryAuditController::class, 'store'])->name('audits.store');
        Route::get('/audits/{audit}', [InventoryAuditController::class, 'staffShow'])->name('audits.show');
        Route::put('/audits/{audit}', [InventoryAuditController::class, 'update'])->name('audits.update');
        Route::post('/audits/{audit}/finalize', [InventoryAuditController::class, 'finalize'])->name('audits.finalize');
    });

// Profile pages belong to the Branch Manager web account, not staff accounts.
Route::middleware(['auth:web', EnsureBranchManager::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
