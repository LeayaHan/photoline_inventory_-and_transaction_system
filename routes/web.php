<?php

use App\Http\Controllers\InventoryAuditController;
use App\Http\Controllers\ManagerAuditController;
use App\Http\Controllers\ManagerTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\AuditDetail;
use App\Models\InventoryAudit;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    if (auth()->user()->isManager()) {

        $todayTransactions = Transaction::whereDate(
            'created_at',
            today()
        )->count();

        $pendingTransactions = Transaction::where(
            'status',
            'Pending'
        )->count();

        $totalAudits = InventoryAudit::count();

        $discrepancies = AuditDetail::where(
            'discrepancy',
            '!=',
            0
        )->count();

        $productCount = Product::count();

        $recentTransactions = Transaction::latest()
            ->take(5)
            ->get();

        $recentAudits = InventoryAudit::latest()
            ->take(5)
            ->get();

        return view('manager.dashboard', compact(
            'todayTransactions',
            'pendingTransactions',
            'totalAudits',
            'discrepancies',
            'productCount',
            'recentTransactions',
            'recentAudits'
        ));
    }

    $transactions = Transaction::latest()
        ->take(5)
        ->get();

    return view(
        'staff.dashboard',
        compact('transactions')
    );

})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Staff Transactions
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'transactions',
        TransactionController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Staff Inventory Audits
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/audits',
        [InventoryAuditController::class, 'index']
    )->name('audits.index');

    Route::get(
        '/audits/create',
        [InventoryAuditController::class, 'create']
    )->name('audits.create');

    Route::post(
        '/audits',
        [InventoryAuditController::class, 'store']
    )->name('audits.store');

    Route::get(
        '/audits/{audit}',
        [InventoryAuditController::class, 'show']
    )->name('audits.show');

    Route::get(
        '/audits/{audit}/edit',
        [InventoryAuditController::class, 'edit']
    )->name('audits.edit');

    Route::put(
        '/audits/{audit}',
        [InventoryAuditController::class, 'update']
    )->name('audits.update');

    Route::put(
        '/audits/{audit}/complete',
        [InventoryAuditController::class, 'complete']
    )->name('audits.complete');


    /*
    |--------------------------------------------------------------------------
    | Manager Review
    |--------------------------------------------------------------------------
    */

    Route::prefix('manager')
        ->name('manager.')
        ->group(function () {

            Route::get(
                '/transactions',
                [ManagerTransactionController::class, 'index']
            )->name('transactions.index');

            Route::get(
                '/transactions/{transaction}',
                [ManagerTransactionController::class, 'show']
            )->name('transactions.show');

            Route::get(
                '/audits',
                [ManagerAuditController::class, 'index']
            )->name('audits.index');

            Route::get(
                '/audits/{audit}',
                [ManagerAuditController::class, 'show']
            )->name('audits.show');

        });


    /*
    |--------------------------------------------------------------------------
    | Manager Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reports',
        [ReportController::class, 'index']
    )->name('reports.index');

    Route::get(
        '/reports/transactions/export',
        [ReportController::class, 'exportTransactions']
    )->name('reports.transactions.export');

    Route::get(
        '/reports/audits/export',
        [ReportController::class, 'exportAudits']
    )->name('reports.audits.export');


    /*
    |--------------------------------------------------------------------------
    | Replenishments
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/replenishments',
        function () {
            return 'Replenishments page';
        }
    )->name('replenishments.index');


    /*
    |--------------------------------------------------------------------------
    | Staff Account Management
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'users',
        UserController::class
    )->except(['show']);

});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';