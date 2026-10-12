<?php

use App\Models\AuditDetail;
use App\Models\InventoryAudit;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

function manager(): User
{
    return User::factory()->create(['role' => 'manager']);
}

function staff(): User
{
    return User::factory()->create(['role' => 'staff']);
}

function makeProduct(array $attrs = []): Product
{
    return Product::create(array_merge([
        'sku' => 'CAM-001',
        'product_name' => 'Digital Camera',
        'category' => 'Camera',
        'unit' => 'piece',
        'quantity' => 10,
        'reorder_level' => 5,
    ], $attrs));
}

/*
|--------------------------------------------------------------------------
| Access / layouts
|--------------------------------------------------------------------------
*/

test('guests are sent to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/products')->assertRedirect('/login');
    $this->get('/audits')->assertRedirect('/login');
});

test('staff pages render inside the shared layout', function () {
    $this->actingAs(staff());
    $transaction = Transaction::create([
        'control_number' => 'TRX-TEST0001',
        'customer_name' => 'Juan',
        'service_type' => 'Printing',
        'transaction_date' => now(),
        'status' => 'Pending',
        'created_by' => auth()->id(),
    ]);

    foreach ([
        route('dashboard'),
        route('transactions.index'),
        route('transactions.create'),
        route('transactions.show', $transaction),
        route('transactions.edit', $transaction),
        route('audits.index'),
        route('audits.create'),
        route('products.index'),
        route('products.create'),
    ] as $url) {
        $this->get($url)->assertOk()->assertSee('Staff Panel', false);
    }
});

test('staff cannot open manager pages', function () {
    $this->actingAs(staff());

    foreach (['/reports', '/users', '/replenishments', '/manager/transactions', '/manager/audits'] as $url) {
        $this->get($url)->assertForbidden();
    }
});

test('manager pages render inside the shared layout', function () {
    $this->actingAs($m = manager());
    $transaction = Transaction::create([
        'control_number' => 'TRX-TEST0002',
        'customer_name' => 'Maria',
        'service_type' => 'Photo',
        'transaction_date' => now(),
        'status' => 'Pending',
        'created_by' => $m->id,
    ]);
    $audit = InventoryAudit::create(['user_id' => $m->id, 'audit_date' => now(), 'status' => 'Ongoing']);
    AuditDetail::create([
        'inventory_audit_id' => $audit->id,
        'product_id' => makeProduct()->id,
        'recorded_qty' => 10, 'counted_qty' => 8, 'discrepancy' => -2,
    ]);

    foreach ([
        route('dashboard'),
        route('manager.transactions.index'),
        route('manager.transactions.show', $transaction),
        route('manager.audits.index'),
        route('manager.audits.show', $audit),
        route('reports.index'),
        route('users.index'),
        route('users.create'),
        route('products.index'),
        route('replenishments.index'),
    ] as $url) {
        $this->get($url)->assertOk()->assertSee('Manager Panel', false);
    }
});

/*
|--------------------------------------------------------------------------
| Inventory CRUD
|--------------------------------------------------------------------------
*/

test('inventory item can be created, updated and deleted', function () {
    $this->actingAs(staff());

    $this->post(route('products.store'), [
        'sku' => 'BAT-001', 'product_name' => 'Camera Battery', 'category' => 'Battery',
        'unit' => 'piece', 'quantity' => 12, 'reorder_level' => 3,
    ])->assertRedirect(route('products.index'));

    $product = Product::where('sku', 'BAT-001')->firstOrFail();
    expect($product->quantity)->toBe(12);

    $this->get(route('products.edit', $product))->assertOk()->assertSee('Camera Battery');

    $this->put(route('products.update', $product), [
        'sku' => 'BAT-001', 'product_name' => 'Camera Battery Pro', 'category' => 'Battery',
        'unit' => 'piece', 'quantity' => 2, 'reorder_level' => 3,
    ])->assertRedirect(route('products.index'));

    expect($product->fresh()->product_name)->toBe('Camera Battery Pro');

    $this->get(route('products.index', ['search' => 'Pro']))->assertOk()->assertSee('Camera Battery Pro');

    $this->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));
    expect(Product::count())->toBe(0);
});

test('inventory validates input and keeps skus unique', function () {
    $this->actingAs(staff());
    makeProduct();

    $this->post(route('products.store'), [
        'sku' => 'CAM-001', 'product_name' => '', 'category' => 'x', 'unit' => 'piece',
        'quantity' => -1, 'reorder_level' => 'abc',
    ])->assertSessionHasErrors(['sku', 'product_name', 'quantity', 'reorder_level']);

    // editing an item with its own sku must still be allowed
    $p = Product::first();
    $this->put(route('products.update', $p), [
        'sku' => 'CAM-001', 'product_name' => 'Digital Camera', 'category' => 'Camera',
        'unit' => 'piece', 'quantity' => 4, 'reorder_level' => 5,
    ])->assertSessionHasNoErrors();
});

test('an item used in an audit cannot be deleted', function () {
    $user = staff();
    $this->actingAs($user);
    $product = makeProduct();
    $audit = InventoryAudit::create(['user_id' => $user->id, 'audit_date' => now(), 'status' => 'Ongoing']);
    AuditDetail::create([
        'inventory_audit_id' => $audit->id, 'product_id' => $product->id,
        'recorded_qty' => 1, 'counted_qty' => 1, 'discrepancy' => 0,
    ]);

    $this->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('error');

    expect(Product::count())->toBe(1);
});

test('replenishment lists only low stock items', function () {
    $this->actingAs(manager());
    makeProduct(['sku' => 'LOW-1', 'product_name' => 'Low Item', 'quantity' => 2]);
    makeProduct(['sku' => 'OK-1', 'product_name' => 'Plenty Item', 'quantity' => 50]);

    $this->get(route('replenishments.index'))
        ->assertOk()
        ->assertSee('Low Item')
        ->assertDontSee('Plenty Item');
});

/*
|--------------------------------------------------------------------------
| Audit flow
|--------------------------------------------------------------------------
*/

test('audit form uses inventory quantities and the full audit flow works', function () {
    $this->actingAs(staff());
    $product = makeProduct(['quantity' => 10]);

    $this->get(route('audits.create'))
        ->assertOk()
        ->assertSee('Digital Camera')
        ->assertSee('value="10"', false);

    $response = $this->post(route('audits.store'), [
        'audit_date' => '2026-10-05',
        'products' => [['product_id' => $product->id, 'recorded_qty' => 10, 'counted_qty' => 8]],
    ]);

    $audit = InventoryAudit::firstOrFail();
    $response->assertRedirect(route('audits.show', $audit));
    expect($audit->details()->first()->discrepancy)->toBe(-2);

    $this->get(route('audits.show', $audit))->assertOk()->assertSee('Shortage');
    $this->get(route('audits.edit', $audit))->assertOk();

    $detail = $audit->details()->first();
    $this->put(route('audits.update', $audit), [
        'audit_date' => '2026-10-06',
        'products' => [['detail_id' => $detail->id, 'recorded_qty' => 10, 'counted_qty' => 10]],
    ])->assertRedirect(route('audits.show', $audit));
    expect($detail->fresh()->discrepancy)->toBe(0);

    $this->put(route('audits.complete', $audit))->assertRedirect(route('audits.show', $audit));
    expect($audit->fresh()->status)->toBe('Completed');

    // completed audits are locked
    $this->get(route('audits.edit', $audit))->assertRedirect(route('audits.show', $audit));
    $this->put(route('audits.update', $audit), [
        'audit_date' => '2026-10-07',
        'products' => [['detail_id' => $detail->id, 'recorded_qty' => 1, 'counted_qty' => 1]],
    ])->assertRedirect(route('audits.show', $audit));
    expect($detail->fresh()->recorded_qty)->toBe(10);
});

test('audit page tells staff to add inventory first when empty', function () {
    $this->actingAs(staff());

    $this->get(route('audits.create'))->assertOk()->assertSee('no inventory items yet');
});

/*
|--------------------------------------------------------------------------
| Transactions + staff accounts still work after the folder reorganisation
|--------------------------------------------------------------------------
*/

test('transaction create / update / void redirects work', function () {
    $this->actingAs(staff());

    $this->post(route('transactions.store'), [
        'customer_name' => 'Juan', 'service_type' => 'Printing',
        'transaction_date' => '2026-10-05', 'item_description' => 'x',
    ])->assertRedirect(route('transactions.index'));

    $transaction = Transaction::firstOrFail();

    $this->put(route('transactions.update', $transaction), [
        'customer_name' => 'Juan D', 'service_type' => 'Printing',
        'transaction_date' => '2026-10-05', 'item_description' => 'x', 'status' => 'Claimed',
    ])->assertRedirect(route('transactions.show', $transaction));

    $this->delete(route('transactions.destroy', $transaction))
        ->assertRedirect(route('transactions.index'));
});

test('manager can create, edit and delete staff accounts', function () {
    $this->actingAs(manager());

    $this->post(route('users.store'), [
        'name' => 'New Staff', 'email' => 'new@staff.test',
        'password' => 'password123', 'password_confirmation' => 'password123',
    ])->assertRedirect(route('users.index'));

    $user = User::where('email', 'new@staff.test')->firstOrFail();
    $this->get(route('users.edit', $user))->assertOk();
    $this->delete(route('users.destroy', $user))->assertRedirect(route('users.index'));
});

test('manager report exports still download', function () {
    $this->actingAs(manager());

    $this->get(route('reports.transactions.export'))->assertOk();
    $this->get(route('reports.audits.export'))->assertOk();
});
