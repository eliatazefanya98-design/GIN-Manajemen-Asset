<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetLoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrower_can_loan_available_asset_and_return_it(): void
    {
        $borrower = User::factory()->create(['role' => 'peminjam']);
        [$asset] = $this->createAvailableAsset();

        $this->actingAs($borrower)
            ->get(route('loans.index'))
            ->assertOk()
            ->assertSee('Laptop Tersedia')
            ->assertSee('Riwayat Peminjaman');

        $this->post(route('loans.store'), [
            'asset_id' => $asset->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_rencana_kembali' => now()->addDays(3)->toDateString(),
            'keperluan' => 'Kegiatan kerja',
        ])->assertRedirect(route('loans.index'));

        $loan = AssetLoan::query()->firstOrFail();
        $this->assertSame('Dipinjam', $loan->status);
        $this->assertSame('Dipinjam', $asset->fresh()->status);
        $this->assertDatabaseHas('asset_histories', ['asset_id' => $asset->id, 'aktivitas' => 'Peminjaman']);

        $this->put(route('loans.return', $loan), [
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'kondisi_sesudah' => 'Baik',
        ])->assertRedirect(route('loans.history'));

        $this->assertSame('Dikembalikan', $loan->fresh()->status);
        $this->assertSame('Aktif', $asset->fresh()->status);
        $this->assertDatabaseHas('asset_histories', ['asset_id' => $asset->id, 'aktivitas' => 'Pengembalian']);
    }

    public function test_damaged_return_puts_asset_into_maintenance(): void
    {
        $borrower = User::factory()->create(['role' => 'peminjam']);
        [$asset] = $this->createAvailableAsset();
        $loan = AssetLoan::query()->create([
            'asset_id' => $asset->id,
            'user_id' => $borrower->id,
            'tanggal_pinjam' => now()->subDay()->toDateString(),
            'tanggal_rencana_kembali' => now()->addDay()->toDateString(),
            'keperluan' => 'Test',
            'status' => 'Dipinjam',
        ]);
        $asset->update(['status' => 'Dipinjam']);

        $this->actingAs($borrower)->put(route('loans.return', $loan), [
            'tanggal_kembali' => now()->toDateString(),
            'kondisi_sesudah' => 'Rusak Ringan',
        ])->assertRedirect(route('loans.history'));

        $this->assertSame('Maintenance', $asset->fresh()->status);
        $this->assertSame('Rusak Ringan', $asset->fresh()->kondisi);
    }

    public function test_staff_cannot_submit_or_access_borrower_loan_pages(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$asset] = $this->createAvailableAsset();

        $this->actingAs($staff)->get(route('loans.create', $asset))->assertForbidden();
        $this->actingAs($staff)->post(route('loans.store'), [])->assertForbidden();
    }

    private function createAvailableAsset(): array
    {
        $category = Category::query()->forceCreate(['name' => 'Elektronik']);
        $location = Location::query()->forceCreate(['name' => 'Ruang A']);
        $asset = Asset::query()->create([
            'kode_asset' => 'AST-' . fake()->unique()->numerify('#####'),
            'nama_asset' => 'Laptop Tersedia',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'status' => 'Aktif',
            'kondisi' => 'Baik',
        ]);

        return [$asset, $location];
    }
}
