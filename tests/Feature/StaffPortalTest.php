<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffPortalTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        $this->seed();
        return User::where('email', 'photolinemanager@gmail.com')->firstOrFail();
    }

    public function test_manager_can_login_with_seeded_credentials(): void
    {
        $this->manager();

        $this->post('/login', ['email' => 'photolinemanager@gmail.com', 'password' => 'photoline@123'])
            ->assertRedirect(route('branch-manager.dashboard', absolute: false));

        $this->assertAuthenticated('web');
        $this->assertGuest('staff');
    }

    public function test_manager_creates_staff_and_staff_logs_in_to_own_ui(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager, 'web')->post(route('branch-manager.users.store'), [
            'first_name' => 'Jane', 'last_name' => 'Cruz', 'email' => 'jane@photoline.test',
            'contact_number' => '09123456789', 'position' => 'Cashier', 'password' => 'secret123',
        ])->assertRedirect(route('branch-manager.users.index'));

        $this->assertDatabaseHas('staff', ['email' => 'jane@photoline.test', 'staff_id' => 'STF-000001']);

        auth('web')->logout();
        $this->flushSession();

        $this->post('/login', ['email' => 'jane@photoline.test', 'password' => 'secret123'])
            ->assertRedirect(route('staff.dashboard', absolute: false));

        $this->assertAuthenticated('staff');

        $this->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('STAFF PORTAL')
            ->assertSee('Welcome, Jane')
            ->assertDontSee('Staff Management');

        foreach (['transactions', 'inventory', 'audits'] as $page) {
            $this->get(route("staff.$page"))->assertOk();
        }
    }

    public function test_staff_cannot_open_manager_pages_and_manager_cannot_open_staff_pages(): void
    {
        $staff = Staff::create([
            'staff_id' => 'STF-000001', 'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@b.test',
            'contact_number' => '1', 'position' => 'Cashier', 'password' => 'secret123',
        ]);

        $this->actingAs($staff, 'staff')->get(route('branch-manager.users.index'))->assertRedirect(route('login'));
        $this->actingAs($staff, 'staff')->get(route('branch-manager.dashboard'))->assertRedirect(route('login'));

        auth('staff')->logout();
        $this->actingAs($this->manager(), 'web')->get(route('staff.dashboard'))->assertRedirect(route('login'));
    }

    public function test_wrong_password_and_logout(): void
    {
        $staff = Staff::create([
            'staff_id' => 'STF-000001', 'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@b.test',
            'contact_number' => '1', 'position' => 'Cashier', 'password' => 'secret123',
        ]);

        $this->post('/login', ['email' => 'a@b.test', 'password' => 'nope'])->assertSessionHasErrors('email');
        $this->assertGuest('staff');

        $this->actingAs($staff, 'staff')->post('/logout')->assertRedirect('/');
        $this->assertGuest('staff');
    }

    public function test_manager_pages_still_render(): void
    {
        $m = $this->manager();
        $this->actingAs($m, 'web')->get(route('branch-manager.dashboard'))->assertOk()->assertSee('photoline-sidebar', false)->assertDontSee('dashboard-layout', false);
        $this->actingAs($m, 'web')->get(route('branch-manager.users.index'))->assertOk();
        $this->actingAs($m, 'web')->get(route('branch-manager.users.create'))->assertOk();
    }
}
