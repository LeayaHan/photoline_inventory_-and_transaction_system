<?php

use App\Http\Controllers\InventoryAuditController;
use App\Http\Controllers\ManagerAuditController;
use App\Http\Controllers\ManagerTransactionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {

    if (auth()->user()->isManager()) {
        return view('dashboard.manager');
    }

    $transactions = Transaction::latest()
        ->take(5)
        ->get();

    return view('dashboard.index', compact('transactions'));

})->middleware(['auth'])->name('dashboard');


Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Staff Transactions
    |--------------------------------------------------------------------------
    */

    Route::resource('transactions', TransactionController::class);


    /*
    |--------------------------------------------------------------------------
    | Staff Inventory Audits
    |--------------------------------------------------------------------------
    */

    Route::get('/audits', [InventoryAuditController::class, 'index'])
        ->name('audits.index');

    Route::get('/audits/create', [InventoryAuditController::class, 'create'])
        ->name('audits.create');

    Route::post('/audits', [InventoryAuditController::class, 'store'])
        ->name('audits.store');

    Route::get('/audits/{audit}', [InventoryAuditController::class, 'show'])
        ->name('audits.show');

    Route::get('/audits/{audit}/edit', [InventoryAuditController::class, 'edit'])
        ->name('audits.edit');

    Route::put('/audits/{audit}', [InventoryAuditController::class, 'update'])
        ->name('audits.update');

    Route::put('/audits/{audit}/complete', [InventoryAuditController::class, 'complete'])
        ->name('audits.complete');


    /*
    |--------------------------------------------------------------------------
    | Manager Review
    |--------------------------------------------------------------------------
    */

    Route::prefix('manager')
        ->name('manager.')
        ->group(function () {

            /*
            | Manager Transaction Records
            | Read/search only
            */

            Route::get('/transactions', [ManagerTransactionController::class, 'index'])
                ->name('transactions.index');

            Route::get('/transactions/{transaction}', [ManagerTransactionController::class, 'show'])
                ->name('transactions.show');


            /*
            | Manager Inventory Audits
            | Read/search only
            */

            Route::get('/audits', [ManagerAuditController::class, 'index'])
                ->name('audits.index');

            Route::get('/audits/{audit}', [ManagerAuditController::class, 'show'])
                ->name('audits.show');
        });


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', function () {
        return 'Reports page';
    })->name('reports.index');


    /*
    |--------------------------------------------------------------------------
    | Replenishments
    |--------------------------------------------------------------------------
    */

    Route::get('/replenishments', function () {
        return 'Replenishments page';
    })->name('replenishments.index');


    /*
    |--------------------------------------------------------------------------
    | Staff Account Management
    |--------------------------------------------------------------------------
    */

    Route::resource('users', UserController::class)
        ->except(['show']);

});


require __DIR__.'/auth.php';