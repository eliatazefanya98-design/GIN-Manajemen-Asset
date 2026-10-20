<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_submit_transfer_and_leader_can_approve_it(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $leader = User::factory()->create(['role' => 'pimpinan']);
        [$asset, $origin, $target] = $this->createTransferAsset();

        $this->actingAs($staff)->post(route('transfers.store'), [
            'asset_id' => $asset->id,
            'target_location_id' => $target->id,
            'reason' => 'Pemindahan ruang kerja',
        ])->assertRedirect();

        $transfer = Transfer::query()->firstOrFail();
        $this->assertSame('menunggu', $transfer->status);

        $this->actingAs($leader)->get(route('approvals.index'))->assertOk();
        $this->actingAs($leader)->get(route('approvals.show', $transfer))->assertOk();

        $this->actingAs($leader)
            ->put(route('approvals.approve', $transfer))
            ->assertRedirect(route('approvals.index'));

        $this->assertSame($target->id, $asset->fresh()->location_id);
        $this->assertSame('disetujui', $transfer->fresh()->status);
        $this->assertDatabaseHas('asset_histories', [
            'asset_id' => $asset->id,
            'aktivitas' => 'Transfer Disetujui',
        ]);
    }

    public function test_leader_rejection_keeps_asset_location_and_stores_reason(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $leader = User::factory()->create(['role' => 'pimpinan']);
        [$asset, $origin, $target] = $this->createTransferAsset();

        $this->actingAs($staff)->post(route('transfers.store'), [
            'asset_id' => $asset->id,
            'target_location_id' => $target->id,
            'reason' => 'Pemindahan ruang kerja',
        ]);
        $transfer = Transfer::query()->firstOrFail();

        $this->actingAs($leader)->put(route('approvals.reject', $transfer), [
            'decision_note' => 'Lokasi belum siap digunakan.',
        ])->assertRedirect(route('approvals.index'));

        $this->assertSame($origin->id, $asset->fresh()->location_id);
        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'status' => 'ditolak',
            'decision_note' => 'Lokasi belum siap digunakan.',
        ]);
    }

    public function test_staff_cannot_access_leader_approval_routes(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$asset, , $target] = $this->createTransferAsset();
        $transfer = Transfer::query()->create([
            'asset_id' => $asset->id,
            'target_location_id' => $target->id,
            'requested_by' => $staff->id,
            'reason' => 'Test',
        ]);

        $this->actingAs($staff)->get(route('approvals.show', $transfer))->assertForbidden();
    }

    private function createTransferAsset(): array
    {
        $category = Category::query()->forceCreate(['name' => 'Elektronik']);
        $origin = Location::query()->forceCreate(['name' => 'Gedung A']);
        $target = Location::query()->forceCreate(['name' => 'Gedung B']);
        $asset = Asset::query()->create([
            'kode_asset' => 'AST-' . fake()->unique()->numerify('#####'),
            'nama_asset' => 'Laptop',
            'category_id' => $category->id,
            'location_id' => $origin->id,
            'status' => 'aktif',
        ]);

        return [$asset, $origin, $target];
    }
}
