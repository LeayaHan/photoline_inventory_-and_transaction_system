<?php

use App\Http\Controllers\InventoryAuditController;
use App\Http\Controllers\ManagerAuditController;
use App\Http\Controllers\ManagerTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReplenishmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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
        return view('manager.dashboard');
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
    | Inventory (CRUD) - staff and manager
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'products',
        ProductController::class
    )->except(['show']);


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

            /*
            | Manager Transactions
            */

            Route::get(
                '/transactions',
                [ManagerTransactionController::class, 'index']
            )->name('transactions.index');

            Route::get(
                '/transactions/{transaction}',
                [ManagerTransactionController::class, 'show']
            )->name('transactions.show');


            /*
            | Manager Audits
            */

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
        [ReplenishmentController::class, 'index']
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