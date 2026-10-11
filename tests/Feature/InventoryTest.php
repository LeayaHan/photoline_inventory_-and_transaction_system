<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::create(['name' => 'M', 'email' => 'm@x.test', 'password' => 'secret123']);
    }

    private function staff(): Staff
    {
        return Staff::create([
            'staff_id' => 'STF-000001', 'first_name' => 'Jane', 'last_name' => 'Cruz', 'email' => 'jane@x.test',
            'contact_number' => '1', 'position' => 'Cashier', 'password' => 'secret123',
        ]);
    }

    private function item(array $o = []): InventoryItem
    {
        return InventoryItem::createWithCode($o + [
            'name' => '4R Paper', 'category' => 'Photo Paper', 'unit' => 'packs',
            'quantity' => 10, 'reorder_level' => 5, 'unit_price' => 100,
        ]);
    }

    public function test_item_code_is_generated(): void
    {
        $this->assertSame('ITM-000001', $this->item()->item_code);
        $this->assertSame('ITM-000002', $this->item(['name' => 'B'])->item_code);
    }

    public function test_manager_cannot_directly_change_inventory(): void
    {
        $item = $this->item();
        $m = $this->manager();

        $this->actingAs($m, 'web')->post('/branch-manager/inventory', ['name' => 'X'])->assertStatus(405);
        $this->actingAs($m, 'web')->get('/branch-manager/inventory/create')->assertNotFound();
        $this->actingAs($m, 'web')->get("/branch-manager/inventory/{$item->id}/edit")->assertNotFound();
        $this->actingAs($m, 'web')->put("/branch-manager/inventory/{$item->id}", ['name' => 'Hacked'])->assertNotFound();
        $this->actingAs($m, 'web')->delete("/branch-manager/inventory/{$item->id}")->assertNotFound();
        $this->actingAs($m, 'web')->post("/branch-manager/inventory/{$item->id}/adjust", ['direction' => 'in', 'quantity' => 50])->assertNotFound();

        $this->assertDatabaseCount('inventory_items', 1);
        $this->assertSame('4R Paper', $item->fresh()->name);
        $this->assertSame(10, $item->fresh()->quantity);
    }

    public function test_staff_stock_request_changes_nothing_until_manager_approves(): void
    {
        $item = $this->item();
        $staff = $this->staff();

        $this->actingAs($staff, 'staff')->post(route('staff.inventory.requests.store'), [
            'type' => 'stock', 'item_id' => $item->id, 'direction' => 'out', 'quantity' => 4, 'reason' => 'Used for order',
        ])->assertRedirect(route('staff.inventory.requests'));

        $req = InventoryRequest::first();
        $this->assertSame('pending', $req->status);
        $this->assertSame(-4, $req->payload['adjustment']);
        $this->assertSame(10, $item->fresh()->quantity);

        auth('staff')->logout();
        $this->actingAs($this->manager(), 'web')
            ->post(route('branch-manager.inventory-requests.approve', ['inventoryRequest' => $req->id]), ['remarks' => 'ok'])
            ->assertSessionHasNoErrors();

        $this->assertSame(6, $item->fresh()->quantity);
        $this->assertSame('approved', $req->fresh()->status);
        $this->assertSame('ok', $req->fresh()->manager_remarks);
    }

    public function test_declining_needs_a_reason_and_does_not_change_stock(): void
    {
        $item = $this->item();
        $req = InventoryRequest::create([
            'type' => 'stock', 'inventory_item_id' => $item->id, 'item_name' => $item->name,
            'payload' => ['adjustment' => 5], 'reason' => 'x', 'status' => 'pending', 'staff_id' => $this->staff()->id,
        ]);
        $m = $this->manager();

        $this->actingAs($m, 'web')->post(route('branch-manager.inventory-requests.decline', ['inventoryRequest' => $req->id]), [])
            ->assertSessionHasErrors('remarks');
        $this->assertSame('pending', $req->fresh()->status);

        $this->actingAs($m, 'web')->post(route('branch-manager.inventory-requests.decline', ['inventoryRequest' => $req->id]), ['remarks' => 'Not needed']);
        $this->assertSame('declined', $req->fresh()->status);
        $this->assertSame(10, $item->fresh()->quantity);
    }

    public function test_cannot_approve_removal_that_would_make_stock_negative(): void
    {
        $item = $this->item(['quantity' => 3]);
        $req = InventoryRequest::create([
            'type' => 'stock', 'inventory_item_id' => $item->id, 'item_name' => $item->name,
            'payload' => ['adjustment' => -9], 'reason' => 'x', 'status' => 'pending', 'staff_id' => $this->staff()->id,
        ]);

        $this->actingAs($this->manager(), 'web')
            ->post(route('branch-manager.inventory-requests.approve', ['inventoryRequest' => $req->id]))
            ->assertSessionHasErrors('request_' . $req->id);

        $this->assertSame(3, $item->fresh()->quantity);
        $this->assertSame('pending', $req->fresh()->status);
    }

    public function test_add_edit_and_delete_requests_are_applied_on_approval(): void
    {
        $staff = $this->staff();
        $m = $this->manager();

        // ADD
        $this->actingAs($staff, 'staff')->post(route('staff.inventory.requests.store'), [
            'type' => 'add', 'name' => 'Album', 'category' => 'Albums', 'unit' => 'pcs',
            'quantity' => 7, 'reorder_level' => 2, 'unit_price' => 300,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('inventory_items', 0);

        $add = InventoryRequest::where('type', 'add')->first();
        $this->actingAs($m, 'web')->post(route('branch-manager.inventory-requests.approve', ['inventoryRequest' => $add->id]));
        $item = InventoryItem::where('name', 'Album')->firstOrFail();
        $this->assertSame(7, $item->quantity);
        $this->assertSame($item->id, $add->fresh()->inventory_item_id);

        // EDIT
        $this->actingAs($staff, 'staff')->post(route('staff.inventory.requests.store'), [
            'type' => 'edit', 'item_id' => $item->id, 'name' => 'Album Deluxe', 'category' => 'Albums', 'unit' => 'pcs',
            'reorder_level' => 2, 'unit_price' => 350, 'reason' => 'New design',
        ])->assertSessionHasNoErrors();
        $edit = InventoryRequest::where('type', 'edit')->first();
        $this->assertSame('Album', $item->fresh()->name);
        $this->actingAs($m, 'web')->post(route('branch-manager.inventory-requests.approve', ['inventoryRequest' => $edit->id]));
        $this->assertSame('Album Deluxe', $item->fresh()->name);
        $this->assertEquals(350, $item->fresh()->unit_price);

        // DELETE
        $this->actingAs($staff, 'staff')->post(route('staff.inventory.requests.store'), [
            'type' => 'delete', 'item_id' => $item->id, 'reason' => 'Discontinued',
        ])->assertSessionHasNoErrors();
        $del = InventoryRequest::where('type', 'delete')->first();
        $this->actingAs($m, 'web')->post(route('branch-manager.inventory-requests.approve', ['inventoryRequest' => $del->id]));
        $this->assertDatabaseCount('inventory_items', 0);
        $this->assertSame('approved', $del->fresh()->status);
        $this->assertSame('Album Deluxe', $del->fresh()->item_name);
    }

    public function test_staff_request_validation(): void
    {
        $item = $this->item(['quantity' => 2]);
        $staff = $this->actingAs($this->staff(), 'staff');

        $staff->post(route('staff.inventory.requests.store'), ['type' => 'stock', 'item_id' => $item->id, 'direction' => 'out', 'quantity' => 5, 'reason' => 'x'])
            ->assertSessionHasErrors('quantity');
        $staff->post(route('staff.inventory.requests.store'), ['type' => 'stock', 'item_id' => $item->id, 'direction' => 'in', 'quantity' => 5])
            ->assertSessionHasErrors('reason');
        $staff->post(route('staff.inventory.requests.store'), ['type' => 'bogus'])->assertSessionHasErrors('type');
        $staff->post(route('staff.inventory.requests.store'), [
            'type' => 'edit', 'item_id' => $item->id, 'name' => $item->name, 'category' => $item->category, 'unit' => $item->unit,
            'reorder_level' => $item->reorder_level, 'unit_price' => $item->unit_price, 'reason' => 'x',
        ])->assertSessionHasErrors('name');

        $this->assertDatabaseCount('inventory_requests', 0);
    }

    public function test_access_control(): void
    {
        $item = $this->item();
        $staff = $this->staff();

        $this->actingAs($staff, 'staff')->get(route('branch-manager.inventory.index'))->assertRedirect(route('login'));
        $this->actingAs($staff, 'staff')->get(route('branch-manager.inventory-requests.index'))->assertRedirect(route('login'));

        auth('staff')->logout();
        $this->actingAs($this->manager(), 'web')->get(route('staff.inventory'))->assertRedirect(route('login'));
    }

    public function test_all_pages_render(): void
    {
        $item = $this->item(['quantity' => 0]);
        $staff = $this->staff();
        $m = $this->manager();

        foreach (['stock', 'edit', 'delete'] as $t) {
            InventoryRequest::create(['type' => $t, 'inventory_item_id' => $item->id, 'item_name' => $item->name,
                'payload' => $t === 'stock' ? ['adjustment' => 3] : ($t === 'edit' ? ['name' => 'New'] : null),
                'reason' => 'r', 'status' => 'pending', 'staff_id' => $staff->id]);
        }

        $this->actingAs($staff, 'staff');
        $this->get(route('staff.inventory'))->assertOk()->assertSee($item->name)->assertSee('Out of stock');
        $this->get(route('staff.inventory.requests'))->assertOk()->assertSee('Pending');
        foreach (['add', 'edit', 'stock', 'delete'] as $t) {
            $this->get(route('staff.inventory.requests.create', ['type' => $t, 'item' => $item->id]))->assertOk();
        }
        $this->get(route('staff.inventory.requests.create', ['type' => 'nope']))->assertNotFound();
        $this->get(route('staff.dashboard'))->assertOk()->assertSee('3 pending requests');

        auth('staff')->logout();
        $this->actingAs($m, 'web');
        $this->get(route('branch-manager.inventory.index'))->assertOk()->assertSee('photoline-sidebar', false);
        $this->get(route('branch-manager.inventory.index'))->assertDontSee('Delete')->assertDontSee('Add Item')->assertDontSee('Edit');
        $this->get(route('branch-manager.inventory-requests.index'))->assertOk()->assertSee('Approve');
        $this->get(route('branch-manager.inventory-requests.index', ['status' => 'all']))->assertOk();
        $this->get(route('branch-manager.dashboard'))->assertOk()->assertSee('Pending Inventory Requests');
    }
}
