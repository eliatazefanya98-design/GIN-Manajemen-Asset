<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_leader_can_filter_asset_report_by_acquisition_period(): void
    {
        $leader = User::factory()->create(['role' => 'pimpinan']);
        $category = Category::query()->forceCreate(['name' => 'Elektronik']);
        Asset::query()->create([
            'kode_asset' => 'AST-IN-RANGE',
            'nama_asset' => 'Asset Dalam Periode',
            'category_id' => $category->id,
            'tanggal_perolehan' => '2026-10-03',
        ]);
        Asset::query()->create([
            'kode_asset' => 'AST-OUT-OF-RANGE',
            'nama_asset' => 'Asset Di Luar Periode',
            'category_id' => $category->id,
            'tanggal_perolehan' => '2026-09-03',
        ]);

        $this->actingAs($leader)
            ->get(route('reports.index', [
                'type' => 'assets',
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-31',
            ]))
            ->assertOk()
            ->assertSee('AST-IN-RANGE')
            ->assertDontSee('AST-OUT-OF-RANGE');
    }

    public function test_leader_can_download_pdf_and_xlsx_reports(): void
    {
        if (! extension_loaded('zip')) {
            $this->markTestSkipped('Aktifkan extension PHP ZIP untuk menguji export XLSX.');
        }

        $leader = User::factory()->create(['role' => 'pimpinan']);
        $filters = [
            'type' => 'assets',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ];

        $this->actingAs($leader)
            ->get(route('reports.export', ['format' => 'pdf', ...$filters]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($leader)
            ->get(route('reports.export', ['format' => 'xlsx', ...$filters]))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
