<?php

namespace Tests\Unit;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AiKnowledgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiKnowledgeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_system_prompt_uses_live_database_and_no_dataset_references(): void
    {
        SystemSetting::current()->update([
            'organization_name' => 'PT Global Internasional Nusantara',
            'organization_unit' => 'Divisi IT',
        ]);

        $category = Category::create(['nama' => 'IT', 'deskripsi' => 'Perangkat IT']);
        $location = Location::create(['nama' => 'Ruang Server', 'gedung' => 'Gedung A', 'lantai' => '1', 'ruangan' => '101']);
        $vendor = Vendor::create(['nama' => 'PT Vendor Jaya', 'kontak' => '08123456789']);

        $asset = Asset::create([
            'kode_asset' => 'AST-GIN-0001',
            'nama_asset' => 'Printer Laser Jet',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'vendor_id' => $vendor->id,
            'merk' => 'HP',
            'tipe' => 'LaserJet Pro',
            'tanggal_perolehan' => now(),
            'harga' => 3500000,
            'status' => 'Aktif',
            'kondisi' => 'Baik',
            'penanggung_jawab' => 'Budi Teknisi',
        ]);

        $service = new AiKnowledgeService();
        $prompt = $service->buildSystemPrompt('Berapa harga Printer Laser Jet?');

        // Memastikan data riil dari database masuk ke prompt
        $this->assertStringContainsString('PT Global Internasional Nusantara', $prompt);
        $this->assertStringContainsString('AST-GIN-0001', $prompt);
        $this->assertStringContainsString('Printer Laser Jet', $prompt);
        $this->assertStringContainsString('Ruang Server', $prompt);
        $this->assertStringContainsString('PT Vendor Jaya', $prompt);

        // Memastikan tidak ada lagi referensi dataset.csv atau AI4I machine telemetry
        $this->assertStringNotContainsString('dataset.csv', $prompt);
        $this->assertStringNotContainsString('AI4I 2020', $prompt);
        $this->assertStringNotContainsString('Tool wear', $prompt);
        $this->assertStringNotContainsString('TWF', $prompt);
        $this->assertStringNotContainsString('HDF', $prompt);
        $this->assertStringNotContainsString('PWF', $prompt);
        $this->assertStringNotContainsString('OSF', $prompt);
        $this->assertStringNotContainsString('RNF', $prompt);
    }
}
