<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Category;
use App\Models\Location;
use App\Models\SystemSetting;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SampleAssetSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $setting = SystemSetting::query()
                ->lockForUpdate()
                ->firstOrCreate([], SystemSetting::defaults());
            $categories = Category::query()->pluck('id', 'nama');
            $locations = Location::query()->pluck('id', 'nama');
            $vendors = Vendor::query()->pluck('id', 'nama');

            $records = [
                [
                    'nama_asset' => 'Printer Epson EcoTank L3250',
                    'category' => 'IT',
                    'location' => 'Lab Komputer 1',
                    'vendor' => 'PT Teknologi Indonesia',
                    'merk' => 'Epson',
                    'tipe' => 'EcoTank L3250',
                    'tanggal_perolehan' => '2025-06-12',
                    'harga' => 3200000,
                    'kondisi' => 'Baik',
                    'status' => 'Aktif',
                    'penanggung_jawab' => 'Operator Lab',
                    'deskripsi' => 'Printer warna untuk kebutuhan administrasi dan laboratorium.',
                ],
                [
                    'nama_asset' => 'Proyektor BenQ MX560',
                    'category' => 'IT',
                    'location' => 'Ruang Rapat',
                    'vendor' => 'PT Teknologi Indonesia',
                    'merk' => 'BenQ',
                    'tipe' => 'MX560',
                    'tanggal_perolehan' => '2024-11-03',
                    'harga' => 7800000,
                    'kondisi' => 'Rusak Ringan',
                    'status' => 'Maintenance',
                    'penanggung_jawab' => 'Petugas Ruang Rapat',
                    'deskripsi' => 'Proyektor ruang rapat; perlu pemeriksaan pada konektor HDMI.',
                ],
                [
                    'nama_asset' => 'Kursi Kerja Ergonomis',
                    'category' => 'Sekretariat',
                    'location' => 'Ruang Rapat',
                    'vendor' => null,
                    'merk' => 'Ergotec',
                    'tipe' => 'Office Chair',
                    'tanggal_perolehan' => '2025-02-18',
                    'harga' => 1850000,
                    'kondisi' => 'Baik',
                    'status' => 'Dipinjam',
                    'penanggung_jawab' => 'Staf Administrasi',
                    'deskripsi' => 'Kursi kerja ergonomis untuk menunjang kegiatan administrasi.',
                ],
                [
                    'nama_asset' => 'Router TP-Link Archer C6',
                    'category' => 'IT',
                    'location' => 'Lab Komputer 1',
                    'vendor' => 'PT Teknologi Indonesia',
                    'merk' => 'TP-Link',
                    'tipe' => 'Archer C6',
                    'tanggal_perolehan' => '2023-08-20',
                    'harga' => 650000,
                    'kondisi' => 'Rusak Ringan',
                    'status' => 'Aktif',
                    'penanggung_jawab' => 'Teknisi Jaringan',
                    'deskripsi' => 'Router cadangan; sinyal Wi-Fi melemah dan dijadwalkan untuk pemeriksaan.',
                ],
                [
                    'nama_asset' => 'Lemari Arsip Besi',
                    'category' => 'Sekretariat',
                    'location' => 'Ruang Rapat',
                    'vendor' => null,
                    'merk' => 'Brother',
                    'tipe' => 'Filing Cabinet 4 Laci',
                    'tanggal_perolehan' => '2022-05-10',
                    'harga' => 2400000,
                    'kondisi' => 'Rusak Berat',
                    'status' => 'Rusak',
                    'penanggung_jawab' => 'Bagian Sekretariat',
                    'deskripsi' => 'Lemari arsip dengan kerusakan berat pada rel laci dan perlu evaluasi penggantian.',
                ],
            ];

            foreach ($records as $record) {
                if (Asset::query()->where('nama_asset', $record['nama_asset'])->exists()) {
                    continue;
                }

                $categoryId = $categories->get($record['category']);
                $locationId = $locations->get($record['location']);

                if (! $categoryId || ! $locationId) {
                    throw new RuntimeException('Kategori atau lokasi untuk data contoh aset belum tersedia.');
                }

                $vendorId = $record['vendor'] ? $vendors->get($record['vendor']) : null;
                unset($record['category'], $record['location'], $record['vendor']);

                $code = $setting->nextAssetCode();
                while (Asset::query()->where('kode_asset', $code)->exists()) {
                    $setting->asset_next_number++;
                    $code = $setting->nextAssetCode();
                }

                $setting->asset_next_number++;
                $asset = Asset::query()->create(array_merge($record, [
                    'kode_asset' => $code,
                    'category_id' => $categoryId,
                    'location_id' => $locationId,
                    'vendor_id' => $vendorId,
                ]));

                AssetHistory::query()->create([
                    'asset_id' => $asset->id,
                    'aktivitas' => 'Asset Dibuat',
                    'deskripsi' => 'Data contoh ditambahkan ke sistem.',
                    'tanggal' => now(),
                ]);
            }

            $setting->save();
        });
    }
}
