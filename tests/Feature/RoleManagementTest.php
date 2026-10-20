<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_each_srs_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'peminjam']);

        foreach (array_keys(User::ROLES) as $role) {
            $this->actingAs($admin)
                ->put(route('users.role.update', $user), ['role' => $role])
                ->assertRedirect();

            $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => $role]);
        }
    }

    public function test_self_registration_creates_a_borrower_role(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Borrower User',
            'email' => 'borrower@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('loans.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'borrower@example.test',
            'role' => 'peminjam',
        ]);
    }

    public function test_login_redirects_to_the_users_role_home_after_visiting_dashboard(): void
    {
        User::factory()->create([
            'email' => 'staff@example.test',
            'password' => 'password123',
            'role' => 'staff',
        ]);

        $this->get('/')->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'email' => 'staff@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('assets.index'));
    }

    public function test_non_admin_cannot_manage_user_roles(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $user = User::factory()->create(['role' => 'peminjam']);

        $this->actingAs($staff)
            ->put(route('users.role.update', $user), ['role' => 'admin'])
            ->assertForbidden();
    }

    public function test_borrower_only_sees_available_assets(): void
    {
        $borrower = User::factory()->create(['role' => 'peminjam']);
        $category = Category::query()->forceCreate(['name' => 'Elektronik']);

        Asset::create([
            'kode_asset' => 'AST-AVAILABLE',
            'nama_asset' => 'Laptop Tersedia',
            'category_id' => $category->id,
            'status' => 'aktif',
        ]);
        Asset::create([
            'kode_asset' => 'AST-LOANED',
            'nama_asset' => 'Laptop Dipinjam',
            'category_id' => $category->id,
            'status' => 'Dipinjam',
        ]);

        $this->actingAs($borrower)
            ->get(route('assets.index'))
            ->assertOk()
            ->assertSee('AST-AVAILABLE')
            ->assertDontSee('AST-LOANED')
            ->assertDontSee('assets.create');
    }

    public function test_asset_history_index_renders_for_leadership(): void
    {
        $leader = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($leader)
            ->get(route('asset-histories.index'))
            ->assertOk()
            ->assertSee('Asset History');
    }

    public function test_admin_can_access_all_existing_application_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach ([
            route('dashboard'),
            route('ai.index'),
            route('assets.index'),
            route('assets.create'),
            route('categories.index'),
            route('categories.create'),
            route('locations.index'),
            route('locations.create'),
            route('vendors.index'),
            route('vendors.create'),
            route('maintenance-records.index'),
            route('maintenance-records.create'),
            route('asset-histories.index'),
            route('settings.index'),
            route('users.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)
            ->get(route('assets.index'))
            ->assertOk()
            ->assertSee('Tambah Asset');
    }
}